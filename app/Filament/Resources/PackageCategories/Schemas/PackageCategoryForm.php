<?php

namespace App\Filament\Resources\PackageCategories\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PackageCategoryForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Hidden::make('collection_group')
                ->default('package')
                ->dehydrated(),
            Section::make('Package Category Details')
                ->description('This category appears as a filter on the public packages page.')
                ->schema([
                    TextInput::make('name')
                        ->label('Category name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->required()
                        ->maxLength(255)
                        ->unique('collections', 'slug', ignoreRecord: true),
                    TextInput::make('summary')
                        ->maxLength(240),
                    Textarea::make('description')
                        ->rows(4)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Packages')
                ->description('Select current packages for this category, or create a new basic package from the plus button.')
                ->schema([
                    Select::make('packages')
                        ->label('Packages in this category')
                        ->relationship('packages', 'title')
                        ->multiple()
                        ->preload()
                        ->searchable()
                        ->createOptionForm([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique('packages', 'slug'),
                            Textarea::make('short_description')
                                ->rows(3)
                                ->maxLength(280)
                                ->columnSpanFull(),
                            Textarea::make('description')
                                ->rows(5)
                                ->columnSpanFull(),
                            TextInput::make('duration')
                                ->maxLength(80),
                            TextInput::make('location')
                                ->maxLength(120),
                            TextInput::make('price_from')
                                ->numeric()
                                ->prefix('AED'),
                            TextInput::make('currency')
                                ->required()
                                ->default('AED')
                                ->maxLength(3),
                            Toggle::make('is_featured')
                                ->label('Featured package')
                                ->inline(false),
                            Toggle::make('is_active')
                                ->default(true)
                                ->required()
                                ->inline(false),
                        ])
                        ->columnSpanFull(),
                ]),
            Section::make('Publishing')
                ->schema([
                    TextInput::make('sort_order')
                        ->required()
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_featured')
                        ->label('Show as public filter')
                        ->helperText('Controls whether this category appears as a filter on the public packages page.')
                        ->required()
                        ->default(true)
                        ->inline(false),
                ])
                ->columns(2),
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
            Section::make('Media')
                ->schema([
                    FileUpload::make('hero_image_path')
                        ->label('Hero image')
                        ->image()
                        ->disk('uploads')
                        ->directory('collections')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->hero_image_path))
                        ->imageEditor(),
                ]),
        ]);
    }
}
