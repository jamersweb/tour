<?php

namespace App\Filament\Resources\BusTourListings\Pages;

use App\Filament\Resources\BusTourListings\BusTourListingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBusTourListings extends ListRecords
{
    protected static string $resource = BusTourListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
