<?php

namespace App\Filament\Resources\BusTourPages\Pages;

use App\Filament\Resources\BusTourPages\BusTourPageResource;
use App\Models\BusTourPage;
use Filament\Resources\Pages\ListRecords;

class ListBusTourPages extends ListRecords
{
    protected static string $resource = BusTourPageResource::class;

    public function mount(): void
    {
        BusTourPage::current();

        parent::mount();
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
