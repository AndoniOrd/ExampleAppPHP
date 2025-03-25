<?php

namespace App\Services;

use App\Models\EmailSubscriber;
use App\Models\EmailTemplate;
use App\Models\MailingList;
use Illuminate\Support\Facades\Log;

class CampaignService
{
    /**
     * Get subscribers from a mailing list
     *
     * @param int $mailingListId
     * @return array
     */
    public function getSubscribersFromMailingList($mailingListId)
    {
        try {
            // Replace this with your actual logic to fetch subscribers 
            // from your mailing list based on your database structure
            $subscribers = EmailSubscriber::where('mailing_list_id', $mailingListId)
                ->where('status', 'active')
                ->get()
                ->map(function ($subscriber) {
                    return [
                        'email' => $subscriber->email,
                        'name' => $subscriber->name,
                        'last_name' => $subscriber->last_name,
                        'subscriber_id' => $subscriber->id,
                        // Add any other fields you need
                    ];
                })
                ->toArray();

            return $subscribers;
        } catch (\Exception $e) {
            Log::error('Failed to fetch subscribers from mailing list', [
                'mailing_list_id' => $mailingListId,
                'error' => $e->getMessage()
            ]);
            
            return [];
        }
    }

    /**
     * Get email template content
     *
     * @param int $templateId
     * @return string|null
     */
    public function getEmailTemplateContent($templateId)
    {
        try {
            // Replace this with your actual logic to fetch template content
            // based on your database structure
            $template = EmailTemplate::find($templateId);
            
            if (!$template) {
                Log::error('Email template not found', [
                    'template_id' => $templateId
                ]);
                return null;
            }
            
            return $template->content;
        } catch (\Exception $e) {
            Log::error('Failed to fetch email template content', [
                'template_id' => $templateId,
                'error' => $e->getMessage()
            ]);
            
            return null;
        }
    }

    /**
     * Personalize email content for a specific subscriber
     *
     * @param string $content
     * @param array $subscriber
     * @return string
     */
    public function personalizeEmailContent($content, array $subscriber)
    {
        // Replace template variables with subscriber data
        $personalizedContent = $content;
        
        // Replace common placeholders
        $replacements = [
            '{{email}}' => $subscriber['email'],
            '{{name}}' => $subscriber['name'] ?? '',
            '{{last_name}}' => $subscriber['last_name'] ?? '',
            '{{full_name}}' => trim(($subscriber['name'] ?? '') . ' ' . ($subscriber['last_name'] ?? '')),
            // Add more placeholders as needed
        ];
        
        foreach ($replacements as $placeholder => $value) {
            $personalizedContent = str_replace($placeholder, $value, $personalizedContent);
        }
        
        return $personalizedContent;
    }
    
    /**
     * Mark a campaign as completed
     *
     * @param int $campaignId
     * @return bool
     */
    public function markCampaignAsCompleted($campaignId)
    {
        try {
            $campaign = \App\Models\CampaignPlanning::find($campaignId);
            
            if (!$campaign) {
                Log::error('Campaign not found when trying to mark as completed', [
                    'campaign_id' => $campaignId
                ]);
                return false;
            }
            
            $campaign->status_type = 'completed';
            $campaign->save();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to mark campaign as completed', [
                'campaign_id' => $campaignId,
                'error' => $e->getMessage()
            ]);
            
            return false;
        }
    }
}