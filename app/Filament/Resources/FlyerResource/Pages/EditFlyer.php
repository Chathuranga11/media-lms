<?php

namespace App\Filament\Resources\FlyerResource\Pages;

use App\Filament\Resources\FlyerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
// Forcing Git to recognize this file

class EditFlyer extends EditRecord
{
    protected static string $resource = FlyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
