<?php

namespace App\Filament\Resources\Tours\Pages;

use App\Filament\Resources\Tours\TourResource;
use App\Filament\Support\MediaUpload;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditTour extends EditRecord
{
    protected static string $resource = TourResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
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
