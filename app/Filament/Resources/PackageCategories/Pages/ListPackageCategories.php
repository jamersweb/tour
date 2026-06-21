<?php

namespace App\Filament\Resources\PackageCategories\Pages;

use App\Filament\Resources\PackageCategories\PackageCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPackageCategories extends ListRecords
{
    protected static string $resource = PackageCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
