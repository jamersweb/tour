<?php

namespace App\Filament\Resources\ExperienceInquiries\Pages;

use App\Filament\Resources\ExperienceInquiries\ExperienceInquiryResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExperienceInquiry extends EditRecord
{
    protected static string $resource = ExperienceInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
        ];
    }
}
