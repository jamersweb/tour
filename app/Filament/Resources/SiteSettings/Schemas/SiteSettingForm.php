<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class SiteSettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Brand')
                ->schema([
                    TextInput::make('site_name')->required()->maxLength(255),
                    TextInput::make('company_legal_name')->maxLength(255),
                    TextInput::make('brand_kicker')->required()->maxLength(50),
                    TextInput::make('brand_name')->required()->maxLength(100),
                    TextInput::make('organization_type')
                        ->required()
                        ->maxLength(100)
                        ->helperText('Schema.org type like TravelAgency or LocalBusiness.'),
                    TextInput::make('website_url')->url()->maxLength(255),
                    TextInput::make('logo_url')->url()->maxLength(255)->label('Header logo URL'),
                    TextInput::make('footer_logo_url')->url()->maxLength(255)->label('Footer logo URL'),
                    TagsInput::make('social_links')
                        ->placeholder('Add a public social profile URL')
                        ->columnSpanFull(),
                    Textarea::make('site_tagline')->rows(2)->required()->columnSpanFull(),
                ])
                ->columns(3),
            Section::make('Contact')
                ->schema([
                    TextInput::make('contact_email')->email()->maxLength(255),
                    TextInput::make('contact_phone')->maxLength(100)->label('Primary phone'),
                    TextInput::make('contact_phone_secondary')->maxLength(100)->label('Secondary phone'),
                    TextInput::make('whatsapp_number')
                        ->maxLength(100)
                        ->helperText('Used for wa.me links site-wide (digits are normalized automatically). Example: +971 52 192 6984'),
                    TextInput::make('license_number')
                        ->maxLength(120)
                        ->label('DTCM / DED license number')
                        ->helperText('Shown in the homepage trust proof and footer once provided.'),
                    TextInput::make('google_reviews_url')
                        ->url()
                        ->maxLength(255)
                        ->label('Google reviews URL'),
                    TextInput::make('tripadvisor_reviews_url')
                        ->url()
                        ->maxLength(255)
                        ->label('Tripadvisor reviews URL'),
                    Textarea::make('contact_address')->rows(3)->columnSpanFull(),
                    TagsInput::make('interest_options')
                        ->placeholder('Add an inquiry interest option')
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Homepage Hero')
                ->schema([
                    TextInput::make('home_hero_eyebrow')->maxLength(120),
                    TextInput::make('home_primary_cta_label')->maxLength(80),
                    TextInput::make('home_secondary_cta_label')->maxLength(80),
                    Textarea::make('home_hero_title')->rows(3)->required()->columnSpanFull(),
                    Textarea::make('home_hero_description')->rows(4)->required()->columnSpanFull(),
                ])
                ->columns(3),
            Section::make('Homepage Sections')
                ->schema([
                    TextInput::make('home_trust_heading')->maxLength(120),
                    TagsInput::make('home_trust_points')->placeholder('Add a trust point')->columnSpanFull(),
                    TagsInput::make('partner_proof')
                        ->placeholder('Add partner, license, payment, or operating proof')
                        ->columnSpanFull(),
                    TextInput::make('home_collections_eyebrow')->maxLength(120),
                    Textarea::make('home_collections_title')->rows(2)->columnSpanFull(),
                    TextInput::make('home_featured_eyebrow')->maxLength(120),
                    Textarea::make('home_featured_title')->rows(2)->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Footer')
                ->schema([
                    Textarea::make('footer_description')->rows(3)->columnSpanFull(),
                    TagsInput::make('footer_build_notes')->placeholder('Add a build note'),
                    TagsInput::make('footer_milestones')->placeholder('Add a milestone'),
                ])
                ->columns(2),
        ]);
    }
}
