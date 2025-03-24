<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CampaignEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Email data.
     *
     * @var array
     */
    public $emailData;

    /**
     * Unique identifier for tracking.
     *
     * @var string
     */
    protected $uuid;

    /**
     * Create a new message instance.
     *
     * @param array $emailData
     * @param string $uuid
     * @return void
     */
    public function __construct(array $emailData, string $uuid)
    {
        $this->emailData = $emailData;
        $this->uuid = $uuid;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view($this->emailData['template'])
            ->with('data', $this->emailData['data'])
            ->subject($this->emailData['subject'])
            ->withSwiftMessage(function ($message) {
                $message->getHeaders()->addTextHeader('X-Campaign-ID', $this->emailData['campaign_id']);
                $message->getHeaders()->addTextHeader('X-Email-ID', $this->uuid);
            });
    }
}