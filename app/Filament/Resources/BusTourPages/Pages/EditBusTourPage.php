<?php

namespace App\Filament\Resources\BusTourPages\Pages;

use App\Filament\Resources\BusTourPages\BusTourPageResource;
use Filament\Resources\Pages\EditRecord;

class EditBusTourPage extends EditRecord
{
    protected static string $resource = BusTourPageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
