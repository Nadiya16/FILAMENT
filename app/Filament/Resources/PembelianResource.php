<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PembelianResource\Pages;
use App\Filament\Resources\PembelianResource\RelationManagers;
use App\Models\Pembelian;
use Barryvdh\DomPDF\Facade\Pdf;
use DragonCode\Support\Facades\Helpers\Arr;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Select;
use Filament\Forms\Set;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Blade;
use PhpParser\Builder\Function_;

class PembelianResource extends Resource
{
    protected static ?string $model = Pembelian::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $label = 'Data Pembelian';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                    Forms\Components\DatePicker::make('tanggal')
                        ->label('Tanggal Pembelian')
                        ->required()
                        ->default(now())->columnSpanFull(),
                    Forms\Components\Select::make('supplier_id')
                        ->options(
                            \App\Models\Supplier::pluck('nama_perusahaan', 'id')
                        )->required()
                        ->label('Pilih Supplier')
                        ->searchable()
                        ->createOptionForm(
                            \App\Filament\Resources\SupplierResource::getForm(),
                        )->createOptionUsing(Function (Array $data): int {
                            return \App\Models\Supplier::create($data)->id;
                        })
                        ->reactive()
                        ->afterStateUpdated(function($state, Set $set){
                        $supplier = \App\Models\Supplier::find($state);
                        $set('email', $supplier->email ?? null);

                    }),
                    Forms\Components\TextInput::make('email')->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('supplier.nama_perusahaan')
                ->label('Nama Supplier'),
                TextColumn::make('supplier.nama')
                ->label('Nama Penghubung'),
                TextColumn::make('tanggal')->dateTime('d F y')->label('Tanggal Pembelian'),

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
                    ->action(function (Pembelian $record) { 
                
                $pdf = PDF::loadView('pembelian', ['record' => $record]); 
                return response()->streamDownload(function () use ($pdf) {
                    echo $pdf->stream();
                }, $record->supplier_id . '.pdf');
                })
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
            'index' => Pages\ListPembelians::route('/'),
            'create' => Pages\CreatePembelian::route('/create'),
            'edit' => Pages\EditPembelian::route('/{record}/edit'),
        ];
    }
}
