<?php

namespace App\Filament\Resources\PembelianResource\Pages;

use App\Filament\Resources\PembelianResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;

class CreatePembelian extends CreateRecord
{
    protected static string $resource = PembelianResource::class;

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Selanjutnya')
                ->submit('create')
                ->keyBindings(['mod+s']),
            
        ];
    }

    protected function getRedirectUrl(): string
    {
        $id = $this->record->id;
        return route('filament.admin.resources.pembelian-items.create',
            [
            'pembelian_id' => $id
            ]
        );
    }
}
  