<?php

namespace App\Filament\Resources\DashboardResource\Widgets;

use App\Models\CampaignPlanning;
use App\Models\EmailContact;
use App\Models\EmailTemplate;
use App\Models\MailingList;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        // Total contacts
        $totalContacts = EmailContact::count();
        
        // Contacts added in the last 30 days
        $newContacts = EmailContact::where('created_at', '>=', Carbon::now()->subDays(30))->count();
        $contactsDescription = $newContacts > 0 
            ? "+{$newContacts} in the last 30 days" 
            : "No new contacts in the last 30 days";
        
        // Active campaigns
        $activeCampaigns = CampaignPlanning::whereIn('status_type', ['scheduled', 'processing'])->count();
        
        // Completed campaigns in the last 30 days
        $recentlyCompleted = CampaignPlanning::where('status_type', 'completed')
            ->where('scheduled_time', '>=', Carbon::now()->subDays(30))
            ->count();
            
        $campaignsDescription = $recentlyCompleted > 0 
            ? "{$recentlyCompleted} completed in the last 30 days" 
            : "No completed campaigns in the last 30 days";
            
        // Available templates
        $emailTemplates = EmailTemplate::count();
        
        // Available mailing lists
        $mailingLists = MailingList::count();
        $averageSize = $mailingLists > 0 
            ? round(EmailContact::count() / $mailingLists) 
            : 0;
        $listsDescription = "Avg. {$averageSize} contacts per list";

        return [
            Stat::make('Total Contacts', $totalContacts)
                ->description($contactsDescription)
                ->descriptionIcon($newContacts > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($newContacts > 0 ? 'success' : 'gray'),
                
            Stat::make('Active Campaigns', $activeCampaigns)
                ->description($campaignsDescription)
                ->descriptionIcon($recentlyCompleted > 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-minus')
                ->color($activeCampaigns > 0 ? 'primary' : 'gray'),
                
            Stat::make('Email Templates', $emailTemplates)
                ->description('Available templates')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
                
            Stat::make('Mailing Lists', $mailingLists)
                ->description($listsDescription)
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),
        ];
    }
}