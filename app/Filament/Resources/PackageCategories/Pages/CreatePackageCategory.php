<?php

namespace App\Filament\Resources\PackageCategories\Pages;

use App\Filament\Resources\PackageCategories\PackageCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePackageCategory extends CreateRecord
{
    protected static string $resource = PackageCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['collection_group'] = 'package';

        return $data;
    }
}
