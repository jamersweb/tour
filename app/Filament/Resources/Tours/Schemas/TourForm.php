<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\HtmlString;

class TourForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Tour Details')
                ->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                    Select::make('category')
                        ->options([
                            'City Tour' => 'City Tour',
                            'Cultural Tour' => 'Cultural Tour',
                            'Adventure Tour' => 'Adventure Tour',
                            'Private Tour' => 'Private Tour',
                        ]),
                    TextInput::make('duration')->maxLength(80),
                    TextInput::make('location')->maxLength(120),
                    TextInput::make('pickup_note')->maxLength(180),
                    Textarea::make('short_description')->rows(3)->maxLength(280)->columnSpanFull(),
                    Textarea::make('description')->rows(8)->columnSpanFull(),
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
                        ->directory('tours')
                        ->imageEditor(),
                    TextInput::make('hero_video_url')
                        ->label('Hero video URL')
                        ->url()
                        ->maxLength(255),
                    FileUpload::make('gallery_images')
                        ->label('Gallery images')
                        ->image()
                        ->multiple()
                        ->disk('uploads')
                        ->directory('tours/gallery')
                        ->reorderable(),
                    TagsInput::make('gallery_videos')
                        ->placeholder('Add a video URL'),
                ]),
            Section::make('Structured Content')
                ->schema([
                    TagsInput::make('highlights')->placeholder('Add a highlight'),
                    TagsInput::make('inclusions')->placeholder('Add an inclusion'),
                    TagsInput::make('exclusions')->placeholder('Add an exclusion'),
                ]),
            Section::make('Product Page Sections')
                ->description('These fields control the extra sections shown on the public tour product page.')
                ->schema([
                    TagsInput::make('important_notices')
                        ->label('Important notices')
                        ->placeholder('Add an important notice'),
                    Repeater::make('expectation_steps')
                        ->label('What to expect')
                        ->schema([
                            TextInput::make('label')->required()->maxLength(80),
                            Textarea::make('copy')->required()->rows(2)->maxLength(300),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                    TagsInput::make('best_for')
                        ->label('Best for')
                        ->placeholder('Add a traveler type or use case'),
                    Repeater::make('faqs')
                        ->label('Frequently asked questions')
                        ->schema([
                            TextInput::make('question')->required()->maxLength(180),
                            Textarea::make('answer')->required()->rows(2)->maxLength(500),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                    Textarea::make('cancellation_policy')
                        ->rows(3)
                        ->maxLength(700)
                        ->columnSpanFull(),
                ])
                ->columns(1),
            Section::make('Booking Preferences')
                ->description('Optional choices shown on checkout for this tour. Leave blank to use default flexible choices.')
                ->schema([
                    TagsInput::make('preferred_time_options')
                        ->label('Preferred tour time choices')
                        ->placeholder('Morning, 09:00 AM, Sunset, Flexible...'),
                    TagsInput::make('preferred_language_options')
                        ->label('Preferred language choices')
                        ->placeholder('English, Arabic, Russian...'),
                    TagsInput::make('tour_options')
                        ->label('Tour option choices')
                        ->placeholder('Standard, Private transfer, Without food tasting...'),
                ])
                ->columns(1),
            Section::make('Pricing')
                ->schema([
                    TextInput::make('price_from')->numeric()->prefix('AED'),
                    TextInput::make('currency')->required()->default('AED')->maxLength(3),
                ])
                ->columns(2),
            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_featured')->required()->inline(false),
                    Toggle::make('is_private')->required()->inline(false),
                    Toggle::make('is_active')->required()->inline(false),
                ])
                ->columns(3),
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
