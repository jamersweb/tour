<?php

namespace App\Filament\Resources\PackageCategories;

use App\Filament\Resources\PackageCategories\Pages\CreatePackageCategory;
use App\Filament\Resources\PackageCategories\Pages\EditPackageCategory;
use App\Filament\Resources\PackageCategories\Pages\ListPackageCategories;
use App\Filament\Resources\PackageCategories\Pages\ViewPackageCategory;
use App\Filament\Resources\PackageCategories\Schemas\PackageCategoryForm;
use App\Filament\Resources\PackageCategories\Schemas\PackageCategoryInfolist;
use App\Filament\Resources\PackageCategories\Tables\PackageCategoriesTable;
use App\Models\Collection;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

class PackageCategoryResource extends Resource
{
    protected static ?string $model = Collection::class;

    protected static ?string $slug = 'package-categories';

    protected static ?string $navigationLabel = 'Package Categories';

    protected static ?string $modelLabel = 'package category';

    protected static ?string $pluralModelLabel = 'package categories';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedSquaresPlus;

    protected static string|UnitEnum|null $navigationGroup = 'Content';

    protected static ?int $navigationSort = 7;

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('collection_group', 'package');
    }

    public static function form(Schema $schema): Schema
    {
        return PackageCategoryForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackageCategoryInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackageCategoriesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPackageCategories::route('/'),
            'create' => CreatePackageCategory::route('/create'),
            'view' => ViewPackageCategory::route('/{record}'),
            'edit' => EditPackageCategory::route('/{record}/edit'),
        ];
    }
}
