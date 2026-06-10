<?php

namespace App\Filament\Resources\Collections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CollectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Collection Details')
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('summary')
                            ->maxLength(240),
                        Textarea::make('description')
                            ->rows(4)
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
                            ->imageEditor(),
                    ]),
                Section::make('Tours and Tickets in this Collection')
                    ->description('Choose which tours and ticket experiences appear under this category/collection.')
                    ->schema([
                        Select::make('experiences')
                            ->label('Tours & tickets')
                            ->relationship('experiences', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                        Select::make('tours')
                            ->label('Guided tours')
                            ->relationship('tours', 'title')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ])
                    ->columns(2),
                Section::make('Publishing')
                    ->schema([
                        TextInput::make('sort_order')
                            ->required()
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_featured')
                            ->required()
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
            ]);
    }
}
