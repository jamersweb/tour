<?php

namespace App\Filament\Resources\Articles\Pages;

use App\Filament\Resources\Articles\ArticleResource;
use App\Filament\Support\MediaUpload;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditArticle extends EditRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
