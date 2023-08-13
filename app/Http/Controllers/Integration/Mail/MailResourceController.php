<?php

namespace App\Http\Controllers\Integration\Mail;

use App\Http\Controllers\ResourceController;

abstract class MailResourceController extends ResourceController
{
    public static function getActionNamespace(): string
    {
        return 'App\Actions\Integration\Mail';
    }
}
