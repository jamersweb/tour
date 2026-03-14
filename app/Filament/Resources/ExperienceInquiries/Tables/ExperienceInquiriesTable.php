<?php

namespace App\Filament\Resources\ExperienceInquiries\Tables;

use App\Models\ExperienceInquiry;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ExperienceInquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('travel_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('guest_count')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('interest')
                    ->badge()
                    ->searchable(),
                TextColumn::make('experience_title')
                    ->label('Experience')
                    ->searchable()
                    ->limit(32),
                TextColumn::make('source')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('follow_up_at')
                    ->dateTime()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->since(),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'contacted' => 'Contacted',
                        'quoted' => 'Quoted',
                        'won' => 'Won',
                        'lost' => 'Lost',
                    ]),
            ])
            ->recordActions([
                Action::make('mark_contacted')
                    ->label('Mark Contacted')
                    ->color('info')
                    ->visible(fn (ExperienceInquiry $record): bool => $record->status === 'new')
                    ->action(fn (ExperienceInquiry $record) => $record->update([
                        'status' => 'contacted',
                        'contacted_at' => $record->contacted_at ?: now(),
                    ])),
                Action::make('mark_won')
                    ->label('Mark Won')
                    ->color('success')
                    ->visible(fn (ExperienceInquiry $record): bool => in_array($record->status, ['quoted', 'contacted'], true))
                    ->action(fn (ExperienceInquiry $record) => $record->update([
                        'status' => 'won',
                    ])),
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
