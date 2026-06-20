<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class PackageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Package Details')
                ->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Textarea::make('short_description')->rows(3)->maxLength(280)->required()->columnSpanFull(),
                    Textarea::make('description')->rows(8)->required()->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Media')
                ->schema([
                    Placeholder::make('current_media_preview')
                        ->label('Current media')
                        ->content(fn ($record) => new HtmlString(self::mediaPreviewHtml($record)))
                        ->columnSpanFull(),
                    FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->image()
                        ->disk('uploads')
                        ->directory('packages')
                        ->imageEditor(),
                    TextInput::make('hero_video_url')
                        ->label('Hero video URL')
                        ->url()
                        ->maxLength(255)
                        ->helperText('Optional MP4 or hosted video URL for the top media area.'),
                    FileUpload::make('gallery_images')
                        ->label('Gallery images')
                        ->image()
                        ->multiple()
                        ->disk('uploads')
                        ->directory('packages/gallery')
                        ->reorderable(),
                    TagsInput::make('gallery_videos')
                        ->placeholder('Add a video URL'),
                ]),
            Section::make('Package Logistics')
                ->schema([
                    TextInput::make('duration')->maxLength(80),
                    TextInput::make('days')->numeric()->minValue(1),
                    TextInput::make('nights')->numeric()->minValue(0),
                    TextInput::make('location')->maxLength(120),
                    TextInput::make('group_size_min')->numeric()->minValue(1),
                    TextInput::make('group_size_max')->numeric()->minValue(1),
                ])
                ->columns(3),
            Section::make('Pricing')
                ->schema([
                    TextInput::make('price_from')->numeric()->prefix('AED'),
                    TextInput::make('sale_price')->numeric()->prefix('AED'),
                    TextInput::make('currency')->required()->maxLength(3)->default('AED'),
                ])
                ->columns(3),
            Section::make('Structured Content')
                ->schema([
                    TagsInput::make('highlights')->placeholder('Add a highlight'),
                    TagsInput::make('inclusions')->placeholder('Add an inclusion'),
                    TagsInput::make('exclusions')->placeholder('Add an exclusion'),
                    Repeater::make('itinerary')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(255),
                            TextInput::make('dayLabel')->label('Day label')->maxLength(40),
                            Textarea::make('description')->rows(4)->required()->columnSpanFull(),
                            TextInput::make('image')->label('Image URL')->maxLength(255)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columns(1),
            Section::make('Product Page Sections')
                ->description('These fields control the extra sections shown on the public package product page.')
                ->schema([
                    TagsInput::make('best_for')
                        ->label('Best fit')
                        ->placeholder('Add a traveler type or use case'),
                    Textarea::make('cancellation_policy')
                        ->rows(3)
                        ->maxLength(700)
                        ->columnSpanFull(),
                    TagsInput::make('important_notices')
                        ->label('Before you go')
                        ->placeholder('Add an important notice'),
                ])
                ->columns(1),
            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_featured')->required()->inline(false),
                    Toggle::make('is_active')->required()->inline(false),
                ])
                ->columns(2),
            Section::make('SEO')
                ->schema([
                    TextInput::make('seo_title')->maxLength(255),
                    Textarea::make('seo_description')->rows(3)->maxLength(320)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }

    protected static function mediaPreviewHtml($record): string
    {
        if (! $record) {
            return '<span style="opacity:.7;">No saved media yet.</span>';
        }

        $parts = [];

        if ($record->hero_image_url) {
            $parts[] = '<div><div style="margin-bottom:8px;font-weight:600;">Hero image</div><img src="'.$record->hero_image_url.'" style="max-width:220px;border-radius:12px;display:block;" /></div>';
        }

        if ($record->hero_video_url) {
            $parts[] = '<div><div style="margin-bottom:8px;font-weight:600;">Hero video</div><video src="'.$record->hero_video_url.'" controls style="max-width:260px;border-radius:12px;display:block;"></video></div>';
        }

        foreach (array_slice($record->gallery_image_urls ?? [], 0, 6) as $url) {
            $galleryImages[] = '<img src="'.$url.'" style="width:96px;height:72px;object-fit:cover;border-radius:10px;" />';
        }

        if (! empty($galleryImages ?? [])) {
            $parts[] = '<div><div style="margin-bottom:8px;font-weight:600;">Gallery images</div><div style="display:flex;gap:8px;flex-wrap:wrap;">'.implode('', $galleryImages).'</div></div>';
        }

        foreach (array_slice($record->gallery_video_urls ?? [], 0, 4) as $url) {
            $galleryVideos[] = '<video src="'.$url.'" controls style="width:140px;border-radius:10px;"></video>';
        }

        if (! empty($galleryVideos ?? [])) {
            $parts[] = '<div><div style="margin-bottom:8px;font-weight:600;">Gallery videos</div><div style="display:flex;gap:8px;flex-wrap:wrap;">'.implode('', $galleryVideos).'</div></div>';
        }

        return implode('<div style="margin-bottom:16px;"></div>', $parts) ?: '<span style="opacity:.7;">No saved media yet.</span>';
    }
}
