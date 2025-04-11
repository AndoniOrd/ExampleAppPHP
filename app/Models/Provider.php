<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'active',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_username',
        'smtp_password',
        'imap_host',
        'imap_port',
        'imap_encryption',
        'imap_username',
        'imap_password',
        'from_address',
        'from_name'
    ];

    protected $casts = [
        'active' => 'boolean',
        'smtp_port' => 'integer',
        'imap_port' => 'integer',
    ];
    
    // Add validation method
    public function isValidSmtpConfig(): bool
    {
        return !empty($this->smtp_host) && 
               !empty($this->smtp_port) && 
               !empty($this->smtp_username) && 
               !empty($this->smtp_password);
    }
     /**
     * Get the SMTP host, ensuring it's not empty
     *
     * @return string
     */
    public function getSmtpHostAttribute($value)
    {
        // If host is empty, log and return fallback
        if (empty($value)) {
            \Log::warning("Provider {$this->name} has empty SMTP host");
            return config('mail.mailers.smtp.host', 'smtp.gmail.com');
        }
        
        return $value;
    }
    
    /**
     * Get the SMTP port, ensuring it's a valid number
     *
     * @return int
     */
    public function getSmtpPortAttribute($value)
    {
        $port = (int)$value;
        if ($port <= 0) {
            return (int)config('mail.mailers.smtp.port', 587);
        }
        
        return $port;
    }

}