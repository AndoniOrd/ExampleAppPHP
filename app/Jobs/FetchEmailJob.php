<?php

namespace App\Jobs;

use App\Models\Provider;
use App\Models\ReceivedEmail;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class FetchEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $provider;
    
    /**
     * Create a new job instance.
     */
    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            if (empty($this->provider->imap_host) || 
                empty($this->provider->imap_port) || 
                empty($this->provider->imap_username) || 
                empty($this->provider->imap_password)) {
                Log::warning('Provider missing IMAP credentials', [
                    'provider_id' => $this->provider->id,
                    'provider_name' => $this->provider->name
                ]);
                return;
            }

            // Construir la cadena de conexión IMAP
            $mailbox = $this->buildImapConnectionString();
            
            // Abrir la conexión IMAP
            $connection = @imap_open($mailbox, $this->provider->imap_username, $this->provider->imap_password);
            
            if (!$connection) {
                Log::error('Failed to connect to IMAP server', [
                    'provider_id' => $this->provider->id,
                    'provider_name' => $this->provider->name,
                    'error' => imap_last_error()
                ]);
                return;
            }
            
            // Buscar correos no leídos
            $emails = imap_search($connection, 'UNSEEN', SE_UID);
            
            if (!$emails) {
                Log::info('No new emails found', [
                    'provider_id' => $this->provider->id,
                    'provider_name' => $this->provider->name
                ]);
                imap_close($connection);
                return;
            }
            
            Log::info('Found new emails', [
                'provider_id' => $this->provider->id,
                'provider_name' => $this->provider->name,
                'count' => count($emails)
            ]);
            
            // Procesar cada correo
            foreach ($emails as $uid) {
                $this->processEmail($connection, $uid);
            }
            
            imap_close($connection);
            
        } catch (\Exception $e) {
            Log::error('Exception while fetching emails', [
                'provider_id' => $this->provider->id,
                'provider_name' => $this->provider->name,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
    
    /**
     * Builds the IMAP connection string
     */
    protected function buildImapConnectionString(): string
    {
        $encryption = strtolower($this->provider->imap_encryption ?? '');
        $flags = '';
        
        if ($encryption === 'ssl') {
            $flags = '/ssl';
        } elseif ($encryption === 'tls') {
            $flags = '/tls';
        }
        
        // Añadir opción de no validar certificados si es necesario
        $flags .= '/novalidate-cert';
        
        return '{'.$this->provider->imap_host.':'.$this->provider->imap_port.$flags.'}INBOX';
    }
    
    /**
     * Process a single email
     */
    protected function processEmail($connection, $uid): void
    {
        try {
            $header = imap_fetchheader($connection, $uid, FT_UID);
            $headerInfo = imap_headerinfo($connection, imap_msgno($connection, $uid));
            $structure = imap_fetchstructure($connection, $uid, FT_UID);
            
            // Extraer el message ID para evitar duplicados
            preg_match('/Message-ID:\s*<(.+?)>/i', $header, $matches);
            $messageId = $matches[1] ?? null;
            
            // Verificar si ya tenemos este correo en nuestra base de datos
            if ($messageId) {
                $existingEmail = ReceivedEmail::where('message_id', $messageId)->first();
                if ($existingEmail) {
                    Log::info('Email already exists in database', [
                        'message_id' => $messageId
                    ]);
                    return;
                }
            }
            
            // Obtener el remitente
            $senderEmail = $headerInfo->from[0]->mailbox . '@' . $headerInfo->from[0]->host;
            $senderName = $headerInfo->from[0]->personal ?? '';
            
            // Obtener el asunto
            $subject = $headerInfo->subject ?? '(Sin asunto)';
            
            // Decodificar el asunto si es necesario
            if (preg_match('/=\?UTF-8\?B\?(.*?)\?=/i', $subject, $matches)) {
                $subject = base64_decode($matches[1]);
            } elseif (preg_match('/=\?UTF-8\?Q\?(.*?)\?=/i', $subject, $matches)) {
                $subject = quoted_printable_decode($matches[1]);
            }
            
            // Obtener la fecha de recepción
            $receivedDate = Carbon::createFromTimestamp(strtotime($headerInfo->date));
            
            // Comprobar si tiene adjuntos
            $hasAttachments = isset($structure->parts) && count($structure->parts) > 1;
            
            // Obtener el contenido
            $content = $this->getEmailBody($connection, $uid, $structure);
            
            // Guardar el email en la base de datos
            ReceivedEmail::create([
                'provider_id' => $this->provider->id,
                'message_id' => $messageId,
                'subject' => $subject,
                'content' => $content,
                'sender_email' => $senderEmail,
                'sender_name' => $senderName,
                'received_at' => $receivedDate,
                'is_read' => false,
                'has_attachments' => $hasAttachments,
                'headers' => json_decode(json_encode($headerInfo), true)
            ]);
            
            // Marcar el correo como leído en el servidor IMAP
            imap_setflag_full($connection, $uid, "\\Seen", ST_UID);
            
            Log::info('Email processed successfully', [
                'message_id' => $messageId,
                'subject' => $subject
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error processing email', [
                'error' => $e->getMessage(),
                'uid' => $uid
            ]);
        }
    }
    
    /**
     * Get the email body based on its structure
     */
    protected function getEmailBody($connection, $uid, $structure)
    {
        // Si es un mensaje simple
        if (!isset($structure->parts)) {
            return $this->getMessagePart($connection, $uid, $structure, 0);
        }
        
        // Para mensajes con múltiples partes
        $body = '';
        
        // Primero intentar obtener la parte HTML
        foreach ($structure->parts as $partNum => $part) {
            if ($part->subtype == 'HTML') {
                $body = $this->getMessagePart($connection, $uid, $part, $partNum + 1);
                break;
            }
        }
        
        // Si no se encontró HTML, buscar texto plano
        if (empty($body)) {
            foreach ($structure->parts as $partNum => $part) {
                if ($part->subtype == 'PLAIN') {
                    $body = $this->getMessagePart($connection, $uid, $part, $partNum + 1);
                    break;
                }
            }
        }
        
        return $body;
    }
    
    /**
     * Get a specific message part
     */
    protected function getMessagePart($connection, $uid, $part, $partNum)
    {
        $data = ($partNum) 
            ? imap_fetchbody($connection, $uid, $partNum, FT_UID) 
            : imap_body($connection, $uid, FT_UID);
        
        // Decodificar según el encoding
        if ($part->encoding == 4) { // QUOTED-PRINTABLE
            $data = quoted_printable_decode($data);
        } elseif ($part->encoding == 3) { // BASE64
            $data = base64_decode($data);
        }
        
        // Convertir a UTF-8 si es necesario
        $charset = $this->getCharset($part);
        if ($charset && $charset != 'UTF-8') {
            $data = @iconv($charset, 'UTF-8//IGNORE', $data);
        }
        
        return $data;
    }
    
    /**
     * Get charset from part
     */
    protected function getCharset($part)
    {
        if (isset($part->parameters)) {
            foreach ($part->parameters as $param) {
                if (strtolower($param->attribute) == 'charset') {
                    return $param->value;
                }
            }
        }
        
        if (isset($part->dparameters)) {
            foreach ($part->dparameters as $param) {
                if (strtolower($param->attribute) == 'charset') {
                    return $param->value;
                }
            }
        }
        
        return null;
    }
    
    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('FetchEmailJob failed', [
            'provider_id' => $this->provider->id,
            'provider_name' => $this->provider->name,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString()
        ]);
    }
}