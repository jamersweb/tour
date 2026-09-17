<?php

namespace App\Filament\Resources\BusTourListings\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusTourListingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Listing Details')
                ->schema([
                    TextInput::make('title')->required()->maxLength(255),
                    TextInput::make('slug')->required()->maxLength(255)->unique(ignoreRecord: true),
                    TextInput::make('route_label')->label('Route label')->maxLength(120),
                    TextInput::make('category_label')->label('Card label')->maxLength(160),
                    TextInput::make('price')->maxLength(120),
                    TextInput::make('panel_price')->label('Detail price')->maxLength(120),
                    Textarea::make('short_description')->rows(3)->columnSpanFull(),
                    Textarea::make('best_for')->rows(3)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Images')
                ->description('Upload images from the admin panel, or paste a public image URL as a fallback.')
                ->schema([
                    FileUpload::make('card_image_path')
                        ->label('Card image')
                        ->image()
                        ->disk('uploads')
                        ->directory('bus-tours/cards')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->card_image_path))
                        ->imageEditor(),
                    TextInput::make('card_image_url')
                        ->label('Card image URL')
                        ->url()
                        ->maxLength(255),
                    FileUpload::make('detail_image_path')
                        ->label('Detail page hero image')
                        ->image()
                        ->disk('uploads')
                        ->directory('bus-tours/details')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->detail_image_path))
                        ->imageEditor(),
                    TextInput::make('detail_image_url')
                        ->label('Detail image URL')
                        ->url()
                        ->maxLength(255),
                    FileUpload::make('gallery_images')
                        ->label('Gallery uploads')
                        ->image()
                        ->multiple()
                        ->disk('uploads')
                        ->directory('bus-tours/gallery')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->gallery_images))
                        ->reorderable()
                        ->columnSpanFull(),
                    TagsInput::make('gallery_image_urls')
                        ->label('Gallery image URLs')
                        ->placeholder('Add image URL')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Structured Content')
                ->schema([
                    TagsInput::make('tags')->placeholder('Add tag')->columnSpanFull(),
                    TagsInput::make('highlights')->placeholder('Add route highlight')->columnSpanFull(),
                    TagsInput::make('included')->placeholder('Add included item')->columnSpanFull(),
                ]),
            Section::make('Publishing')
                ->schema([
                    Toggle::make('is_active')->required()->inline(false)->default(true),
                    TextInput::make('sort_order')->numeric()->required()->default(0),
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
