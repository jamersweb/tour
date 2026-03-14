<?php

namespace App\Filament\Resources\ExperienceInquiries;

use App\Filament\Resources\ExperienceInquiries\Pages\EditExperienceInquiry;
use App\Filament\Resources\ExperienceInquiries\Pages\ListExperienceInquiries;
use App\Filament\Resources\ExperienceInquiries\Pages\ViewExperienceInquiry;
use App\Filament\Resources\ExperienceInquiries\Schemas\ExperienceInquiryForm;
use App\Filament\Resources\ExperienceInquiries\Schemas\ExperienceInquiryInfolist;
use App\Filament\Resources\ExperienceInquiries\Tables\ExperienceInquiriesTable;
use App\Models\ExperienceInquiry;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class ExperienceInquiryResource extends Resource
{
    protected static ?string $model = ExperienceInquiry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Sales';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return ExperienceInquiryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExperienceInquiryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExperienceInquiriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getPages(): array
    {
        return [
            'index' => ListExperienceInquiries::route('/'),
            'view' => ViewExperienceInquiry::route('/{record}'),
            'edit' => EditExperienceInquiry::route('/{record}/edit'),
        ];
    }
}
