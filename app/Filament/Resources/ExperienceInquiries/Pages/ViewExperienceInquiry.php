<?php

namespace App\Filament\Resources\ExperienceInquiries\Pages;

use App\Filament\Resources\ExperienceInquiries\ExperienceInquiryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExperienceInquiry extends ViewRecord
{
    protected static string $resource = ExperienceInquiryResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $this->record->load([
            'activityLogs' => fn ($query) => $query->latest('created_at')->with('user'),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
