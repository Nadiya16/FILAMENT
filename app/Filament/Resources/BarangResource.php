<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BarangResource\Pages;
use App\Filament\Resources\BarangResource\RelationManagers;
use App\Models\Barang;
use App\Models\Pelanggan;
use App\Models\Pembelian;
use Barryvdh\DomPDF\Facade\Pdf;
use DragonCode\Contracts\Cashier\Resources\Model;
use Filament\Tables\Actions\EditAction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Blade;


class BarangResource extends Resource
{
    protected static ?string $model = Barang::class;

    protected static ?string $navigationIcon = 'heroicon-o-face-smile';
    protected static ?string $label = 'Data Barang';

    public static function form(Form $form): Form
    {
        Pembelian::class;
        return $form
            ->schema([
                TextInput::make('kode')
                    ->required(),
                TextInput::make('nama')
                    ->label('Nama Barang'),
                TextInput::make('harga')
                    ->label('Harga Barang'),
                TextInput::make('stok')
                    ->disabledOn('edit')
                    ->label('Stok Awal'),
                Select::make('satuan')
                    ->options([
                        'pcs' => 'Pcs',
                        'lusin' => 'lusin',
                    ]),
                
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('kode')->searchable(),
                TextColumn::make('nama')->searchable(),
                TextColumn::make('harga'),
                TextColumn::make('stok'),
                TextColumn::make('satuan'),
            ])
            ->filters([
                //
            ])
            ->actions([
                EditAction::make(),

                Tables\Actions\Action::make('pdf')
                    ->label('PDF')
                    ->color('success')
                    ->icon('heroicon-o-face-smile')
                    ->action(function (Barang $record) { 
                        $pdf = PDF::loadView('barang', ['record' => $record]); 
                        return response()->streamDownload(function () use ($pdf) {
                            echo $pdf->stream();
                        }, $record->kode . '.pdf');
                    }),
                
            ])
            ->bulkActions([
                BulkActionGroup::make([
                DeleteBulkAction::make(),
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
            'index' => Pages\ListBarangs::route('/'),
            'create' => Pages\CreateBarang::route('/create'),
            'edit' => Pages\EditBarang::route('/{record}/edit'),
        ];
    }
}
