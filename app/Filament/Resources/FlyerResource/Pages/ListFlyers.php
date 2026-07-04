<?php

namespace App\Filament\Resources\FlyerResource\Pages;

use App\Filament\Resources\FlyerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
// Forcing Git to recognize this file

class ListFlyers extends ListRecords
{
    protected static string $resource = FlyerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
