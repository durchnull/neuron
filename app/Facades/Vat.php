<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 *
 * @todo method
 *
 * @see VatService
 */
class Vat extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'vat';
    }
}
