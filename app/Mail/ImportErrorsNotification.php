<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ImportErrorsNotification extends Mailable
{
    use SerializesModels; // Remove Queueable to avoid queue issues

    /**
     * The error data.
     *
     * @var array
     */
    public $errors;

    /**
     * Create a new message instance.
     *
     * @param array $errors
     * @return void
     */
    public function __construct(array $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(
                config('mail.from.address', 'noreply@example.com'),
                config('mail.from.name', 'Email Import System')
            ),
            subject: 'Important: Email Contacts Import Errors Report',
            tags: ['import', 'errors'],
            metadata: [
                'error_count' => $this->getTotalErrorCount(),
                'system' => 'email_import'
            ],
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.import-errors',
            text: 'emails.import-errors-plain',
            with: [
                'errorCount' => $this->getTotalErrorCount(),
                'timestamp' => now()->format('Y-m-d H:i:s'),
            ],
        );
    }

    /**
     * Get the total number of errors.
     *
     * @return int
     */
    protected function getTotalErrorCount(): int
    {
        return count($this->errors['existing'] ?? []) +
               count($this->errors['invalid'] ?? []) +
               count($this->errors['empty'] ?? []);
    }
    
    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments(): array
    {
        // If there are many errors, include a CSV file
        if ($this->getTotalErrorCount() > 20) {
            return [
                $this->generateErrorCsv(),
            ];
        }
        
        return [];
    }
    
    /**
     * Generate CSV file with errors
     * 
     * @return \Illuminate\Mail\Mailables\Attachment
     */
    protected function generateErrorCsv()
    {
        $csvData = "Row,Email,Type,Reason\n";
        
        foreach (['existing', 'invalid', 'empty'] as $type) {
            foreach ($this->errors[$type] ?? [] as $error) {
                $csvData .= "{$error['row']},{$error['email']},{$type},\"{$error['reason']}\"\n";
            }
        }
        
        $tempFile = tempnam(sys_get_temp_dir(), 'import_errors_');
        file_put_contents($tempFile, $csvData);
        
        return \Illuminate\Mail\Mailables\Attachment::fromPath($tempFile)
            ->as('import_errors_' . date('Y-m-d') . '.csv')
            ->withMime('text/csv');
    }
}