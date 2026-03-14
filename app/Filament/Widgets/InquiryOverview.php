<?php

namespace App\Filament\Widgets;

use App\Models\ExperienceInquiry;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class InquiryOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $newCount = ExperienceInquiry::query()->where('status', 'new')->count();
        $openPipeline = ExperienceInquiry::query()->openPipeline()->count();
        $overdueFollowUps = ExperienceInquiry::query()->overdueFollowUp()->count();
        $wonCount = ExperienceInquiry::query()->where('status', 'won')->count();

        return [
            Stat::make('New Leads', $newCount)
                ->description('Fresh inquiries awaiting first contact')
                ->color($newCount > 0 ? 'warning' : 'gray'),
            Stat::make('Open Pipeline', $openPipeline)
                ->description('All leads not yet won or lost')
                ->color('primary'),
            Stat::make('Overdue Follow-Ups', $overdueFollowUps)
                ->description('Open leads with missed follow-up dates')
                ->color($overdueFollowUps > 0 ? 'danger' : 'success'),
            Stat::make('Won Leads', $wonCount)
                ->description('Closed opportunities marked won')
                ->color('success'),
        ];
    }
}
