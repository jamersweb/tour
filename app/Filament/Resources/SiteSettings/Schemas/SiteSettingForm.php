<?php

namespace App\Filament\Resources\SiteSettings\Schemas;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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
                    TextInput::make('logo_url')->url()->maxLength(255),
                    TagsInput::make('social_links')
                        ->placeholder('Add a public social profile URL')
                        ->columnSpanFull(),
                    Textarea::make('site_tagline')->rows(2)->required()->columnSpanFull(),
                ])
                ->columns(3),
            Section::make('Contact')
                ->schema([
                    TextInput::make('contact_email')->email()->maxLength(255),
                    TextInput::make('contact_phone')->maxLength(100),
                    TextInput::make('whatsapp_number')->maxLength(100),
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
