<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class MailConfigHelper
{
    /**
     * Test an SMTP configuration by opening a socket to host:port
     *
     * @param  array  $config  host, port, encryption
     * @return bool
     */
    public static function testConnection(array $config): bool
    {
        $host = $config['host'];
        $port = $config['port'];
        $encryption = strtolower($config['encryption'] ?? '');

        // Pick transport prefix
        $transport = $encryption === 'ssl' ? 'ssl' : 'tcp';
        $timeout   = 5; // seconds

        $errNo  = 0;
        $errStr = '';
        $fp = @fsockopen(
            "{$transport}://{$host}",
            $port,
            $errNo,
            $errStr,
            $timeout
        );

        if (! $fp) {
            Log::error("SMTP connection failed", compact('host','port','errNo','errStr'));
            return false;
        }

        // If explicit TLS is requested, initiate upgrade
        if ($encryption === 'tls') {
            $cryptoOK = stream_socket_enable_crypto(
                $fp,
                true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT
            );
            if (! $cryptoOK) {
                Log::error("SMTP STARTTLS failed for {$host}:{$port}");
                fclose($fp);
                return false;
            }
        }

        fclose($fp);
        return true;
    }
}
