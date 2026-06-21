<?php

namespace App\Filament\Resources\Collections\Widgets;

use App\Models\Collection;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class ParentCategoriesTable extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Parent categories')
            ->description('Only these parent categories are used on the website menu and public filters. Create and manage their subcategories in the table below.')
            ->query(fn (): Builder => Collection::query()
                ->selectRaw('MIN(id) as id, collection_group')
                ->selectRaw('COUNT(*) as subcategories_count')
                ->selectRaw('SUM(CASE WHEN is_featured THEN 1 ELSE 0 END) as visible_subcategories_count')
                ->whereIn('collection_group', ['location', 'activity', 'package'])
                ->groupBy('collection_group'))
            ->columns([
                TextColumn::make('collection_group')
                    ->label('Parent category')
                    ->formatStateUsing(fn (?string $state): string => match ($state) {
                        'location' => 'By Location',
                        'package' => 'Package Category',
                        default => 'By Activity Type',
                    })
                    ->badge(),
                TextColumn::make('subcategories_count')
                    ->label('Total subcategories')
                    ->numeric(),
                TextColumn::make('visible_subcategories_count')
                    ->label('Shown on website')
                    ->numeric(),
                IconColumn::make('is_locked')
                    ->label('Fixed parent')
                    ->state(true)
                    ->boolean(),
            ])
            ->paginated(false);
    }
}
