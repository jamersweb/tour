<?php

namespace App\Filament\Resources\Collections\Pages;

use App\Filament\Resources\Collections\CollectionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCollections extends ListRecords
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make('createLocation')
                ->label('Add Location Subcategory')
                ->modalHeading('New Location Subcategory')
                ->fillForm([
                    'collection_group' => 'location',
                    'is_featured' => true,
                    'sort_order' => 0,
                ])
                ->createAnother(false),
            CreateAction::make('createActivity')
                ->label('Add Activity Type Subcategory')
                ->modalHeading('New Activity Type Subcategory')
                ->fillForm([
                    'collection_group' => 'activity',
                    'is_featured' => true,
                    'sort_order' => 0,
                ])
                ->createAnother(false),
        ];
    }
}
