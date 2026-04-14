<?php

namespace App\Filament\Resources\Experiences\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

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
                        FileUpload::make('hero_image_path')
                            ->label('Hero image')
                            ->helperText('Main image for listings and the first slide on the public experience page.')
                            ->image()
                            ->disk('uploads')
                            ->directory('experiences')
                            ->imageEditor(),
                        FileUpload::make('gallery_images')
                            ->label('Gallery images')
                            ->helperText('Additional photos for the experience page hero carousel (drag to reorder).')
                            ->image()
                            ->multiple()
                            ->disk('uploads')
                            ->directory('experiences/gallery')
                            ->reorderable(),
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
}
