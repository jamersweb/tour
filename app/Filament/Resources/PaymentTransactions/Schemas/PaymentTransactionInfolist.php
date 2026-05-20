<?php

namespace App\Filament\Resources\PaymentTransactions\Schemas;

use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PaymentTransactionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextEntry::make('reference'),
            TextEntry::make('status'),
            TextEntry::make('gateway'),
            TextEntry::make('amount')->money('AED'),
            TextEntry::make('customer_name'),
            TextEntry::make('customer_email'),
            TextEntry::make('customer_phone')->placeholder('-'),
            TextEntry::make('confirmation_sent_at')->dateTime()->placeholder('-'),
            TextEntry::make('reconciled_at')->dateTime()->placeholder('-'),
            TextEntry::make('reconciledBy.name')->label('Reconciled By')->placeholder('-'),
            TextEntry::make('refunded_at')->dateTime()->placeholder('-'),
            TextEntry::make('refundedBy.name')->label('Refunded By')->placeholder('-'),
            TextEntry::make('gateway_order_ref')->placeholder('-'),
            TextEntry::make('gateway_payment_ref')->placeholder('-'),
            TextEntry::make('paid_at')->dateTime()->placeholder('-'),
            RepeatableEntry::make('cart_items')
                ->label('Cart items')
                ->schema([
                    TextEntry::make('title'),
                    TextEntry::make('travelDate')->label('Travel date')->placeholder('-'),
                    TextEntry::make('guestCount')->label('Guests'),
                    TextEntry::make('lineTotalFormatted')->label('Line total'),
                ])
                ->columnSpanFull(),
            RepeatableEntry::make('travelers')
                ->schema([
                    TextEntry::make('position')->label('#'),
                    TextEntry::make('name'),
                    TextEntry::make('email'),
                    TextEntry::make('phone')->placeholder('-'),
                    TextEntry::make('email_sent_at')->dateTime()->placeholder('-'),
                    TextEntry::make('whatsapp_sent_at')->dateTime()->placeholder('-'),
                ])
                ->columnSpanFull(),
            RepeatableEntry::make('activityLogs')
                ->label('Activity log (checkout, gateway, booking emails, admin)')
                ->schema([
                    TextEntry::make('created_at')->dateTime()->label('When'),
                    TextEntry::make('action')->label('Action')->badge(),
                    TextEntry::make('description')->placeholder('—')->columnSpanFull(),
                    TextEntry::make('user.name')->label('By')->placeholder('System / gateway'),
                    TextEntry::make('properties')
                        ->label('Details')
                        ->formatStateUsing(fn (?array $state): string => $state
                            ? (string) json_encode($state, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
                            : '—')
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
            TextEntry::make('notes')->placeholder('-')->columnSpanFull(),
        ]);
    }
}
