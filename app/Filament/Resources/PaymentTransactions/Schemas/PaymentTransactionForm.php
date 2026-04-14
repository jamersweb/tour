<?php

namespace App\Filament\Resources\PaymentTransactions\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PaymentTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Transaction')
                ->schema([
                    TextInput::make('reference')->disabled(),
                    TextInput::make('gateway')->disabled(),
                    Select::make('status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'authorized' => 'Authorized',
                            'failed' => 'Failed',
                            'cancelled' => 'Cancelled',
                            'refunded' => 'Refunded',
                        ])
                        ->required(),
                    TextInput::make('amount')->disabled(),
                    TextInput::make('currency')->disabled(),
                    DateTimePicker::make('paid_at'),
                    DateTimePicker::make('confirmation_sent_at')->disabled(),
                    DateTimePicker::make('reconciled_at')->disabled(),
                    DateTimePicker::make('refunded_at')->disabled(),
                ])
                ->columns(2),
            Section::make('Customer')
                ->schema([
                    TextInput::make('customer_name')->disabled(),
                    TextInput::make('customer_email')->disabled(),
                    TextInput::make('customer_phone')->disabled(),
                    TextInput::make('travel_date')->disabled(),
                    TextInput::make('guest_count')->disabled(),
                ])
                ->columns(2),
            Section::make('Gateway References')
                ->schema([
                    TextInput::make('gateway_order_ref')->disabled(),
                    TextInput::make('gateway_payment_ref')->disabled(),
                    TextInput::make('payment_url')->disabled()->columnSpanFull(),
                    Textarea::make('notes')->rows(4)->columnSpanFull(),
                ])
                ->columns(2),
        ]);
    }
}
