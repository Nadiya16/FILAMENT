<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianItemResource\Pages;
use App\Filament\Resources\PembelianItemResource\RelationManagers;
use App\Models\Pembelian;
use App\Models\PembelianItem;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PembelianItemResource extends Resource
{
    protected static ?string $model = PembelianItem::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        $pembelian = new Pembelian();
        if(request()->filled('pembelian_id')){
            $pembelian = \App\Models\Pembelian::find(request('pembelian_id'));
        }
        
        return $form
            ->schema([
                Grid::make()
                ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                    ->label('Tanggal Pembelian')
                    ->required()
                    ->default($pembelian->tanggal)
                    ->disabled(),
                Forms\Components\TextInput::make('supplier_id')
                    ->label('Supplier')
                    ->required()
                    ->disabled()
                    ->default($pembelian->supplier?->nama),
                Forms\Components\TextInput::make('supplier_id')
                    ->label('Email Supplier')
                    ->required()
                    ->disabled()
                    ->default($pembelian->supplier?->email),
                ])->columns(3),
                Grid::make()
                ->schema([
                    Forms\Components\Select::make('barang_id')
                    ->label('Barang')
                    ->required()
                    ->options(
                        \App\Models\Barang::all()->pluck('nama', 'id')
                    )->reactive()
                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                        $barang = \App\Models\Barang::find($state);
                        $set('harga', $barang->harga ?? null);
                        $jumlah = $get('jumlah');
                        $total = $jumlah * $barang->harga;
                        $set('total', $total);
                    }),
                    Forms\Components\TextInput::make('harga')
                        ->label('Harga Barang'),
                    Forms\Components\TextInput::make('jumlah')
                        ->reactive()
                        ->afterStateUpdated(function($state, Set $set, Get $get){
                            $jumlah = $state;
                            $harga = $get('harga');
                            $total = $jumlah * $harga;
                            $set('total', $total);
                        })
                        ->label('Jumlah Barang')->required()->default(1),
                    Forms\Components\TextInput::make('total')
                        ->disabled()
                        ->label('Total Harga'),
                    ])->columns(4),
                
                Forms\Components\Hidden::make('pembelian_id')
                    ->default(request('pembelian_id'))
                    
            
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            TextColumn::make('pembelian.tanggal')
                ->dateTime('d F Y')
                ->label('Tanggal Pembelian'),
            TextColumn::make('barang.nama')
                ->label('Nama Barang'),
            TextColumn::make('jumlah')
                ->label('Jumlah Barang'),
            TextColumn::make('total')
                ->label('Total Harga')
                ->formatStateUsing(function ($record) {
                    return $record->jumlah * $record->harga;
                })
                ->money('idr'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPembelianItems::route('/'),
            'create' => Pages\CreatePembelianItem::route('/create'),
            'edit' => Pages\EditPembelianItem::route('/{record}/edit'),
        ];
    }
}
