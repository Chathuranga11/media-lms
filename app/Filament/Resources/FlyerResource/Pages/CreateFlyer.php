<?php

namespace App\Filament\Resources\FlyerResource\Pages;
// Forcing Git to recognize this file

use App\Filament\Resources\FlyerResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateFlyer extends CreateRecord
{
    protected static string $resource = FlyerResource::class;
}
