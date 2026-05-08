<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class ExperienceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Experience Details')
                    ->schema([
                        TextInput::make('title')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        Select::make('category')
                            ->required()
                            ->options([
                                'Desert' => 'Desert',
                                'Yacht' => 'Yacht',
                                'City' => 'City',
                                'Water & Family' => 'Water & Family',
                            ]),
                        TextInput::make('tag')
                            ->maxLength(40),
                        TextInput::make('duration')
                            ->maxLength(80),
                        TextInput::make('location')
                            ->maxLength(120),
                        TextInput::make('pickup_note')
                            ->maxLength(180),
                        TextInput::make('short_description')
                            ->maxLength(240)
                            ->columnSpanFull(),
                        Textarea::make('hero_summary')
                            ->rows(3)
                            ->columnSpanFull(),
                        Textarea::make('description')
                            ->rows(5)
                            ->columnSpanFull(),
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
                            ->helperText('Main image for listings and the first slide on the public experience page.')
                            ->image()
                            ->disk('uploads')
                            ->directory('experiences')
                            ->imageEditor(),
                        TextInput::make('hero_video_url')
                            ->label('Hero video URL')
                            ->url()
                            ->maxLength(255)
                            ->helperText('Optional MP4 or hosted video URL for the top media area.'),
                        FileUpload::make('gallery_images')
                            ->label('Gallery images')
                            ->helperText('Additional photos for the experience page hero carousel (drag to reorder).')
                            ->image()
                            ->multiple()
                            ->disk('uploads')
                            ->directory('experiences/gallery')
                            ->reorderable(),
                        TagsInput::make('gallery_videos')
                            ->placeholder('Add a video URL'),
                    ])
                    ->columns(1),
                Section::make('Structured Content')
                    ->schema([
                        TagsInput::make('highlights')
                            ->placeholder('Add a highlight'),
                        TagsInput::make('inclusions')
                            ->placeholder('Add an inclusion'),
                        TagsInput::make('exclusions')
                            ->placeholder('Add an exclusion'),
                    ])
                    ->columns(1),
                Section::make('Collections and Pricing')
                    ->schema([
                        Select::make('collections')
                            ->relationship('collections', 'name')
                            ->multiple()
                            ->preload(),
                        TextInput::make('price_from')
                            ->numeric()
                            ->prefix('AED'),
                        TextInput::make('currency')
                            ->required()
                            ->default('AED')
                            ->maxLength(3),
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        TextInput::make('homepage_sort_order')
                            ->numeric()
                            ->default(0)
                            ->helperText('Lower values appear first on the homepage grid.'),
                    ])
                    ->columns(2),
                Section::make('Publishing')
                    ->schema([
                        Toggle::make('is_featured')
                            ->required()
                            ->inline(false),
                        Toggle::make('show_on_homepage')
                            ->required()
                            ->inline(false),
                        Toggle::make('is_private')
                            ->required()
                            ->inline(false),
                        Toggle::make('is_active')
                            ->required()
                            ->inline(false),
                    ])
                    ->columns(4),
                Section::make('SEO')
                    ->schema([
                        TextInput::make('seo_title')
                            ->maxLength(255),
                        Textarea::make('seo_description')
                            ->rows(3)
                            ->maxLength(320)
                            ->columnSpanFull(),
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
