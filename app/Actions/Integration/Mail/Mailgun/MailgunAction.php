<?php

namespace App\Actions\Integration\Mail\Mailgun;

use App\Actions\Action;
use App\Models\Integration\Mail\Mailgun;

abstract class MailgunAction extends Action
{
    final public static function targetClass(): string
    {
        return Mailgun::class;
    }
}
