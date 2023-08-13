<?php

namespace App\Actions\Integration\Mail\Mailgun;

class MailgunDeleteAction extends MailgunAction
{
    public static function rules(): array
    {
        return [];
    }

    protected function gate(array $attributes): void
    {
    }

    protected function apply(): void
    {
        $this->target->delete();
    }
}
