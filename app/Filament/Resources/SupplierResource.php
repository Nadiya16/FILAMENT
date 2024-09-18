<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SupplierResource\Pages;
use App\Filament\Resources\SupplierResource\RelationManagers;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SupplierResource extends Resource
{
    protected static ?string $model = Supplier::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Data Supplier';


    public static function getForm(){
        return[
            Forms\Components\TextInput::make('nama_perusahaan'),
            Forms\Components\TextInput::make('nama')
                ->label('Nama Kontak')
                ->required()
                ->minLength(3),
            Forms\Components\TextInput::make('no_hp')
                ->label('Nomor Handphone')->type('number'),
            Forms\Components\TextInput::make('email')->type('email'),
            Forms\Components\Textarea::make('alamat')->columnSpanFull(),
            Forms\Components\Textarea::make('alamat_pribadi')->columnSpanFull(),
        ];
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema(
               self::getForm()
            );
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_perusahaan')->searchable(),
                Tables\Columns\TextColumn::make('nama')->searchable(),
                Tables\Columns\TextColumn::make('no_hp')->label('Nomor Handphone'),
                Tables\Columns\TextColumn::make('email')->label('Email'),
                Tables\Columns\TextColumn::make('alamat')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('pdf')
                ->label('PDF')
                ->color('success')
                ->icon('heroicon-o-rectangle-stack')
                ->action(function (Supplier $record) { 
                    $pdf = PDF::loadView('supplier', ['record' => $record]); 
                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->stream();
                    }, $record->nama . '.pdf');
                }),
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
            'index' => Pages\ListSuppliers::route('/'),
            'create' => Pages\CreateSupplier::route('/create'),
            'edit' => Pages\EditSupplier::route('/{record}/edit'),
        ];
    }
}
