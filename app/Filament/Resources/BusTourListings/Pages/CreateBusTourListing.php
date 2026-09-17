<?php

namespace App\Filament\Resources\BusTourListings\Pages;

use App\Filament\Resources\BusTourListings\BusTourListingResource;
use App\Filament\Support\MediaUpload;
use Filament\Resources\Pages\CreateRecord;

class CreateBusTourListing extends CreateRecord
{
    protected static string $resource = BusTourListingResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return MediaUpload::normalizeData($data, ['card_image_path', 'detail_image_path', 'gallery_images']);
    }
}
