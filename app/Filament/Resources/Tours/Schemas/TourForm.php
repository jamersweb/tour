<?php

namespace App\Filament\Resources\Tours\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
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
                    Hidden::make('category')
                        ->default('Activity'),
                    TextInput::make('duration')->maxLength(80),
                    TextInput::make('location')->maxLength(120),
                    TextInput::make('pickup_note')->maxLength(180),
                    TextInput::make('experience_type')
                        ->label('Experience type')
                        ->placeholder('Private Tour, Shared Tour, Entry Ticket...')
                        ->maxLength(120),
                    TextInput::make('transfer_option')
                        ->label('Transfer option')
                        ->placeholder('Hotel Pickup Included, Transfer Available, No Transfer...')
                        ->maxLength(120),
                    TextInput::make('booking_type')
                        ->label('Booking type')
                        ->placeholder('Instant Confirmation, Subject to Availability...')
                        ->maxLength(120),
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
                    TagsInput::make('tour_options')
                        ->label('Tour language choices')
                        ->placeholder('English, Arabic, Russian...'),
                    Repeater::make('booking_options')
                        ->label('Priced booking options')
                        ->helperText('Optional. Use for choices such as Shared, Private, VIP, or ticket variants that need different prices.')
                        ->schema([
                            TextInput::make('label')
                                ->required()
                                ->maxLength(120)
                                ->placeholder('Private tour'),
                            TextInput::make('price')
                                ->required()
                                ->numeric()
                                ->prefix('AED')
                                ->placeholder('950')
                                ->label('Adult price'),
                            TextInput::make('child_price')
                                ->numeric()
                                ->prefix('AED')
                                ->placeholder('750')
                                ->label('Kid price')
                                ->helperText('Optional. Leave blank to use the product-level kid price or the adult option price.'),
                            Textarea::make('description')
                                ->rows(2)
                                ->maxLength(240)
                                ->placeholder('Short note shown under the option.'),
                        ])
                        ->columns(2)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ])
                ->columns(1),
            Section::make('Pricing')
                ->schema([
                    TextInput::make('price_from')->label('Adult price from')->numeric()->prefix('AED'),
                    TextInput::make('child_price_from')
                        ->label('Kid price from')
                        ->numeric()
                        ->prefix('AED')
                        ->helperText('Optional. Used for kids aged 3 to 11. Leave blank to use the adult price.'),
                    TextInput::make('currency')->required()->default('AED')->maxLength(3),
                ])
                ->columns(2),
            Section::make('Collections')
                ->schema([
                    Select::make('collections')
                        ->label('Subcategories')
                        ->helperText('Assign this tour to one or more header subcategories.')
                        ->relationship('collections', 'name')
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
            Section::make('Availability Calendar')
                ->description('Block dates or seasonal periods when this tour should not be bookable online.')
                ->schema([
                    TagsInput::make('unavailable_dates')
                        ->label('Unavailable dates')
                        ->placeholder('2026-12-25')
                        ->helperText('Use YYYY-MM-DD format.'),
                    Repeater::make('unavailable_periods')
                        ->label('Unavailable date ranges')
                        ->schema([
                            TextInput::make('start')
                                ->required()
                                ->placeholder('2026-06-01')
                                ->helperText('YYYY-MM-DD'),
                            TextInput::make('end')
                                ->required()
                                ->placeholder('2026-08-31')
                                ->helperText('YYYY-MM-DD'),
                            TextInput::make('label')
                                ->maxLength(120)
                                ->placeholder('Summer closure'),
                        ])
                        ->columns(3)
                        ->defaultItems(0)
                        ->columnSpanFull(),
                ])
                ->columns(1),
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
