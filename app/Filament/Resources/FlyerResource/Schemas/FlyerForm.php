<?php

namespace App\Filament\Resources\FlyerResource\Schemas;

use Filament\Schemas\Schema;
// Forcing Git to recognize this file
class FlyerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                //
            ]);
    }
}
