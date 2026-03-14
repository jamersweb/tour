<?php

namespace App\Filament\Resources\ExperienceInquiries\Pages;

use App\Filament\Resources\ExperienceInquiries\ExperienceInquiryResource;
use Filament\Resources\Pages\ListRecords;

class ListExperienceInquiries extends ListRecords
{
    protected static string $resource = ExperienceInquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
