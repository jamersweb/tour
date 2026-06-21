<?php

namespace App\Filament\Resources\PackageCategories\Pages;

use App\Filament\Resources\PackageCategories\PackageCategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditPackageCategory extends EditRecord
{
    protected static string $resource = PackageCategoryResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data['collection_group'] = 'package';

        return $data;
    }
}
