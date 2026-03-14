<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ExperienceInquiries\ExperienceInquiryResource;
use App\Models\ExperienceInquiry;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentInquiries extends TableWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Inquiries';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                ExperienceInquiry::query()
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('medium'),
                TextColumn::make('experience_title')
                    ->label('Experience')
                    ->placeholder('General inquiry')
                    ->limit(28),
                TextColumn::make('status')
                    ->badge(),
                TextColumn::make('follow_up_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('created_at')
                    ->since(),
            ])
            ->recordAction(null)
            ->actions([
                Action::make('open')
                    ->label('Open')
                    ->url(fn (ExperienceInquiry $record): string => ExperienceInquiryResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
