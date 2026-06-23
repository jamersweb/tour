<?php

namespace App\Filament\Resources\Packages\Pages;

use App\Filament\Resources\Packages\PackageResource;
use App\Filament\Support\MediaUpload;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPackage extends EditRecord
{
    protected static string $resource = PackageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return MediaUpload::normalizeData($data, ['hero_image_path', 'gallery_images']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MediaUpload::normalizeData($data, ['hero_image_path', 'gallery_images']);
    }
}
