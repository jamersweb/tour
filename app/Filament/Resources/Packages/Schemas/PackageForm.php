<?php

namespace App\Filament\Resources\Packages\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                    FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->image()
                        ->disk('uploads')
                        ->directory('packages')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->hero_image_path))
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
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->gallery_images))
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
            Section::make('Package Categories')
                ->description('Assign this package to one or more package filter categories shown on the public packages page.')
                ->schema([
                    Select::make('collections')
                        ->label('Package categories')
                        ->relationship(
                            'collections',
                            'name',
                            modifyQueryUsing: fn ($query) => $query->where('collection_group', 'package')->orderBy('collections.sort_order')->orderBy('collections.name'),
                        )
                        ->multiple()
                        ->preload()
                        ->searchable(),
                ]),
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
                    Textarea::make('best_for')
                        ->label('Best fit')
                        ->placeholder('Add one best-fit item per line')
                        ->rows(4)
                        ->formatStateUsing(fn ($state) => self::textareaState($state))
                        ->dehydrateStateUsing(fn ($state) => self::textareaLines($state))
                        ->columnSpanFull(),
                    Textarea::make('cancellation_policy')
                        ->rows(3)
                        ->maxLength(700)
                        ->columnSpanFull(),
                    Textarea::make('important_notices')
                        ->label('Before you go')
                        ->placeholder('Add one notice per line')
                        ->rows(4)
                        ->formatStateUsing(fn ($state) => self::textareaState($state))
                        ->dehydrateStateUsing(fn ($state) => self::textareaLines($state))
                        ->columnSpanFull(),
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

    protected static function textareaState($state): ?string
    {
        if ($state === null || $state === '') {
            return null;
        }

        if (! is_array($state)) {
            return (string) $state;
        }

        return collect($state)
            ->filter(fn ($item) => filled($item))
            ->implode("\n");
    }

    protected static function textareaLines($state): array
    {
        if (is_array($state)) {
            return collect($state)
                ->map(fn ($item) => trim((string) $item))
                ->filter()
                ->values()
                ->all();
        }

        return collect(preg_split('/\r\n|\r|\n/', (string) $state))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }
}
