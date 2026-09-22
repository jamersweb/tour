<?php

namespace App\Filament\Resources\BusTourPages\Pages;

use App\Filament\Resources\BusTourPages\BusTourPageResource;
use Filament\Resources\Pages\EditRecord;

class EditBusTourPage extends EditRecord
{
    protected static string $resource = BusTourPageResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        if (isset($data['gallery_items']) && is_array($data['gallery_items'])) {
            $data['gallery_items'] = collect($data['gallery_items'])
                ->map(function (mixed $item): array {
                    $item = is_array($item) ? $item : [];
                    $item['uploaded_images'] ??= $item['image_uploads'] ?? [];
                    $item['uploaded_videos'] ??= $item['video_uploads'] ?? [];

                    return $item;
                })
                ->values()
                ->all();
        }

        return $data;
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}
