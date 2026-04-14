<?php

namespace App\Filament\Resources\Packages\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
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
                        ->imageEditor(),
                    FileUpload::make('gallery_images')
                        ->label('Gallery images')
                        ->image()
                        ->multiple()
                        ->disk('uploads')
                        ->directory('packages/gallery')
                        ->reorderable(),
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
}
