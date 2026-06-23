<?php

namespace App\Filament\Resources\PackageCategories\Pages;

use App\Filament\Resources\PackageCategories\PackageCategoryResource;
use App\Filament\Support\MediaUpload;
use Filament\Resources\Pages\EditRecord;

class EditPackageCategory extends EditRecord
{
    protected static string $resource = PackageCategoryResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return MediaUpload::normalizeData($data, ['hero_image_path']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $data = MediaUpload::normalizeData($data, ['hero_image_path']);
        $data['collection_group'] = 'package';

        return $data;
    }
}
