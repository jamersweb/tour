<?php

namespace App\Filament\Resources\PaymentTransactions\Tables;

use App\Models\PaymentTransaction;
use App\Services\Payments\PaymentOperationsService;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PaymentTransactionsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('reference')->searchable()->sortable(),
            TextColumn::make('status')->badge()->sortable(),
            TextColumn::make('payable_type')
                ->label('Type')
                ->formatStateUsing(fn (string $state) => class_basename($state)),
            TextColumn::make('payable.title')
                ->label('Item')
                ->searchable(),
            TextColumn::make('customer_name')->searchable(),
            TextColumn::make('amount')->money('AED')->sortable(),
            TextColumn::make('travelers_count')->counts('travelers')->label('Travelers'),
            TextColumn::make('confirmation_sent_at')->since()->placeholder('-'),
            TextColumn::make('gateway_order_ref')->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('created_at')->since()->sortable(),
        ])->filters([
            SelectFilter::make('status')->options([
                'pending' => 'Pending',
                'paid' => 'Paid',
                'authorized' => 'Authorized',
                'failed' => 'Failed',
                'cancelled' => 'Cancelled',
                'refunded' => 'Refunded',
            ]),
        ])->recordActions([
            ViewAction::make(),
            EditAction::make(),
            ActionGroup::make([
                Action::make('downloadInvoice')
                    ->label('Invoice PDF')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->url(fn (PaymentTransaction $record) => route('admin.payment-transactions.invoice', $record))
                    ->openUrlInNewTab(),
                Action::make('reconcile')
                    ->icon('heroicon-o-check-badge')
                    ->form([
                        Textarea::make('notes')->rows(3),
                    ])
                    ->action(function (PaymentTransaction $record, array $data): void {
                        app(PaymentOperationsService::class)->reconcile(
                            $record,
                            auth()->user(),
                            status: 'paid',
                            notes: $data['notes'] ?? null,
                        );

                        Notification::make()->title('Payment reconciled as paid.')->success()->send();
                    }),
                Action::make('markRefunded')
                    ->label('Refund')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->form([
                        Textarea::make('notes')->rows(3)->required(),
                    ])
                    ->action(function (PaymentTransaction $record, array $data): void {
                        app(PaymentOperationsService::class)->refund(
                            $record,
                            auth()->user(),
                            notes: $data['notes'] ?? null,
                        );

                        Notification::make()->title('Payment marked as refunded.')->success()->send();
                    }),
                Action::make('resendConfirmation')
                    ->label('Resend Confirmation')
                    ->icon('heroicon-o-envelope')
                    ->action(function (PaymentTransaction $record): void {
                        app(PaymentOperationsService::class)->resendConfirmation($record);

                        Notification::make()->title('Confirmation resent.')->success()->send();
                    }),
            ]),
        ])->toolbarActions([]);
    }
}
