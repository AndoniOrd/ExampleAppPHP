<?php

namespace App\Jobs;

use App\Services\EmailNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class RetryImportErrorNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The error data to be sent
     *
     * @var array
     */
    protected $errorData;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = [300, 600, 1800];

    /**
     * Create a new job instance.
     *
     * @param array $errorData
     * @return void
     */
    public function __construct(array $errorData)
    {
        $this->errorData = $errorData;
    }

    /**
     * Execute the job.
     *
     * @param EmailNotificationService $notificationService
     * @return void
     */
    public function handle(EmailNotificationService $notificationService)
    {
        Log::info('Retrying import error notification', [
            'attempt' => $this->attempts(),
            'existing_count' => count($this->errorData['existing'] ?? []),
            'invalid_count' => count($this->errorData['invalid'] ?? []),
            'empty_count' => count($this->errorData['empty'] ?? []),
        ]);

        // Try to send the notification
        $success = $notificationService->sendImportErrorNotifications($this->errorData);

        if (!$success) {
            Log::warning('Retry attempt failed', [
                'attempt' => $this->attempts(),
                'next_retry' => $this->retryAfter()
            ]);

            // If we haven't reached the maximum tries, throw an exception to retry
            if ($this->attempts() < $this->tries) {
                // This will cause the job to be released back to the queue
                $this->release($this->backoff[$this->attempts() - 1] ?? 300);
            } else {
                Log::error('All retry attempts failed for import error notification');
            }
        } else {
            Log::info('Retry succeeded on attempt ' . $this->attempts());
        }
    }

    /**
     * Calculate retry delay
     *
     * @return int
     */
    protected function retryAfter()
    {
        return $this->backoff[$this->attempts() - 1] ?? 300;
    }
}