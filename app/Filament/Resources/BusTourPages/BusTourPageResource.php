<?php

namespace App\Filament\Resources\BusTourPages;

use App\Filament\Resources\BusTourPages\Pages\EditBusTourPage;
use App\Filament\Resources\BusTourPages\Pages\ListBusTourPages;
use App\Filament\Resources\BusTourPages\Schemas\BusTourPageForm;
use App\Filament\Resources\BusTourPages\Tables\BusTourPagesTable;
use App\Models\BusTourPage;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class BusTourPageResource extends Resource
{
    protected static ?string $model = BusTourPage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Panoramic Bus Page';

    protected static ?string $modelLabel = 'Panoramic Bus Page';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return BusTourPageForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BusTourPagesTable::configure($table);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBusTourPages::route('/'),
            'edit' => EditBusTourPage::route('/{record}/edit'),
        ];
    }
}
