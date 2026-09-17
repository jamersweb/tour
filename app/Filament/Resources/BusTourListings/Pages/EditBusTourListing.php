<?php

namespace App\Filament\Resources\BusTourListings\Pages;

use App\Filament\Resources\BusTourListings\BusTourListingResource;
use App\Filament\Support\MediaUpload;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBusTourListing extends EditRecord
{
    protected static string $resource = BusTourListingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        return MediaUpload::normalizeData($data, ['card_image_path', 'detail_image_path', 'gallery_images']);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return MediaUpload::normalizeData($data, ['card_image_path', 'detail_image_path', 'gallery_images']);
    }
}
