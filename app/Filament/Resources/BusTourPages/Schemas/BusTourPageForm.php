<?php

namespace App\Filament\Resources\BusTourPages\Schemas;

use App\Filament\Support\MediaUpload;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class BusTourPageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('SEO')
                ->schema([
                    TextInput::make('seo_title')->maxLength(255),
                    Textarea::make('seo_description')->rows(3)->maxLength(320)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Hero')
                ->schema([
                    TextInput::make('hero_eyebrow')->maxLength(120),
                    TextInput::make('hero_primary_cta_label')->maxLength(80),
                    TextInput::make('hero_secondary_cta_label')->maxLength(80),
                    TextInput::make('hero_title')->required()->maxLength(255)->columnSpanFull(),
                    Textarea::make('hero_description')->required()->rows(4)->columnSpanFull(),
                    TextInput::make('hero_fomo_line')->maxLength(255)->columnSpanFull(),
                    Repeater::make('hero_facts')
                        ->schema([
                            TextInput::make('value')->required()->maxLength(120),
                            TextInput::make('label')->required()->maxLength(180),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columns(3),
            Section::make('Intro And Benefits')
                ->schema([
                    Textarea::make('intro_mark')->rows(3)->helperText('Use line breaks for the stacked side text.'),
                    TextInput::make('intro_eyebrow')->maxLength(120),
                    Textarea::make('intro_title')->rows(2)->columnSpanFull(),
                    Textarea::make('intro_copy')->rows(3)->columnSpanFull(),
                    TextInput::make('choice_media_label')->maxLength(180),
                    TextInput::make('choice_media_title')->maxLength(180),
                    Textarea::make('choice_media_copy')->rows(2)->columnSpanFull(),
                    Repeater::make('choice_lines')
                        ->schema([
                            TextInput::make('number')->required()->maxLength(10),
                            TextInput::make('title')->required()->maxLength(180),
                            Textarea::make('copy')->required()->rows(2)->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                    TextInput::make('choice_primary_cta_label')->maxLength(80),
                    TextInput::make('choice_secondary_cta_label')->maxLength(80),
                ])
                ->columns(2),
            Section::make('Tour Packages')
                ->description('Controls the heading around the listing cards. Create, edit, sort, and upload images for actual tours in Content > Panoramic Bus Listings.')
                ->schema([
                    TextInput::make('routes_eyebrow')->maxLength(120),
                    Textarea::make('routes_title')->rows(2)->columnSpanFull(),
                    Textarea::make('routes_copy')->rows(3)->columnSpanFull(),
                    Textarea::make('availability_note')->rows(3)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Details And Before Booking')
                ->schema([
                    TextInput::make('details_eyebrow')->maxLength(120),
                    Textarea::make('details_title')->rows(2)->columnSpanFull(),
                    Textarea::make('details_copy')->rows(3)->columnSpanFull(),
                    TextInput::make('before_booking_eyebrow')->maxLength(120),
                    Textarea::make('before_booking_title')->rows(2)->columnSpanFull(),
                    Textarea::make('before_booking_copy')->rows(3)->columnSpanFull(),
                    Repeater::make('before_booking_cards')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(180),
                            Textarea::make('copy')->required()->rows(3)->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Private Bus Section')
                ->schema([
                    TextInput::make('private_eyebrow')->maxLength(120),
                    TextInput::make('private_cta_label')->maxLength(80),
                    Textarea::make('private_title')->rows(2)->columnSpanFull(),
                    Textarea::make('private_copy')->rows(3)->columnSpanFull(),
                    FileUpload::make('private_image_path')
                        ->label('Private image upload')
                        ->image()
                        ->disk('uploads')
                        ->directory('bus-tour-page/private')
                        ->formatStateUsing(fn ($state) => MediaUpload::formatState($state))
                        ->dehydrateStateUsing(fn ($state, $record) => MediaUpload::dehydrateState($state, $record?->private_image_path))
                        ->imageEditor(),
                    TextInput::make('private_image_url')
                        ->label('Private image URL fallback')
                        ->url()
                        ->maxLength(255),
                    Repeater::make('private_lines')
                        ->schema([
                            TextInput::make('label')->required()->maxLength(120),
                            TextInput::make('value')->required()->maxLength(180),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Audience')
                ->schema([
                    TextInput::make('audience_eyebrow')->maxLength(120),
                    Textarea::make('audience_title')->rows(2)->columnSpanFull(),
                    Textarea::make('audience_copy')->rows(3)->columnSpanFull(),
                    Repeater::make('audiences')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(180),
                            Textarea::make('copy')->required()->rows(3)->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Gallery')
                ->description('Add public image or video URLs. Uploaded media can be pasted here after upload.')
                ->schema([
                    TextInput::make('gallery_eyebrow')->maxLength(120),
                    Textarea::make('gallery_title')->rows(2)->columnSpanFull(),
                    Textarea::make('gallery_copy')->rows(3)->columnSpanFull(),
                    Repeater::make('gallery_items')
                        ->schema([
                            TextInput::make('title')->required()->maxLength(180),
                            Textarea::make('copy')->rows(2)->columnSpanFull(),
                            TagsInput::make('images')->placeholder('Add image URL')->columnSpanFull(),
                            TagsInput::make('videos')->placeholder('Add video URL')->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                    Textarea::make('gallery_note')->rows(2)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Enquiry Form')
                ->schema([
                    TextInput::make('enquiry_eyebrow')->maxLength(120),
                    TextInput::make('enquiry_submit_label')->maxLength(80),
                    TextInput::make('enquiry_processing_label')->maxLength(80),
                    Textarea::make('enquiry_title')->rows(2)->columnSpanFull(),
                    Textarea::make('enquiry_copy')->rows(3)->columnSpanFull(),
                    TextInput::make('enquiry_whatsapp_label')->maxLength(180)->columnSpanFull(),
                    TextInput::make('whatsapp_url')->url()->maxLength(255)->columnSpanFull(),
                    Textarea::make('enquiry_note')->rows(3)->columnSpanFull(),
                ])
                ->columns(3),
            Section::make('FAQ And Sticky CTA')
                ->schema([
                    TextInput::make('faq_eyebrow')->maxLength(120),
                    Textarea::make('faq_title')->rows(2)->columnSpanFull(),
                    Repeater::make('faqs')
                        ->schema([
                            TextInput::make('question')->required()->maxLength(255),
                            Textarea::make('answer')->required()->rows(3)->columnSpanFull(),
                        ])
                        ->columns(1)
                        ->columnSpanFull(),
                    TextInput::make('sticky_label')->maxLength(80),
                    TextInput::make('sticky_text')->maxLength(120),
                    TextInput::make('sticky_cta_label')->maxLength(80),
                ])
                ->columns(3),
        ]);
    }
}
