<?php

namespace App\Filament\Resources\BusTourListings;

use App\Filament\Resources\BusTourListings\Pages\CreateBusTourListing;
use App\Filament\Resources\BusTourListings\Pages\EditBusTourListing;
use App\Filament\Resources\BusTourListings\Pages\ListBusTourListings;
use App\Filament\Resources\BusTourListings\Schemas\BusTourListingForm;
use App\Filament\Resources\BusTourListings\Tables\BusTourListingsTable;
use App\Models\BusTourListing;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BusTourListingResource extends Resource
{
    protected static ?string $model = BusTourListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPlusCircle;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Panoramic Bus Listings';

    protected static ?string $modelLabel = 'Panoramic Bus Listing';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return BusTourListingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusTourListingsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusTourListings::route('/'),
            'create' => CreateBusTourListing::route('/create'),
            'edit' => EditBusTourListing::route('/{record}/edit'),
        ];
    }
}
