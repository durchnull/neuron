<?php

namespace App\Contracts\Engine;

use App\Models\Engine\Merchant;

interface MerchantServiceContract
{
    public function new(): Merchant;

    public function get(): Merchant;

    public function set(Merchant $merchant): MerchantServiceContract;

    public function setByToken(string $token): MerchantServiceContract;

    public function id(): string;
}
