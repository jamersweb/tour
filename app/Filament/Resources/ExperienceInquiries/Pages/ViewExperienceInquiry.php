<?php

namespace App\Filament\Resources\ExperienceInquiries\Pages;

use App\Filament\Resources\ExperienceInquiries\ExperienceInquiryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExperienceInquiry extends ViewRecord
{
    protected static string $resource = ExperienceInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
