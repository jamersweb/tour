<?php

namespace App\Filament\Resources\Collections\Pages;

use App\Filament\Resources\Collections\CollectionResource;
use App\Filament\Support\MediaUpload;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditCollection extends EditRecord
{
    protected static string $resource = CollectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return MediaUpload::normalizeData($data, ['hero_image_path']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MediaUpload::normalizeData($data, ['hero_image_path']);
    }
}
