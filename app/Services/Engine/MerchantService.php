<?php

namespace App\Services\Engine;

use App\Contracts\Engine\MerchantServiceContract;
use App\Models\Engine\Merchant;

class MerchantService implements MerchantServiceContract
{
    protected ?Merchant $merchant;

    public function __construct()
    {
    }

    public function new(): Merchant
    {
        return new Merchant();
    }

    public function get(): Merchant
    {
        return $this->merchant;
    }

    public function set(Merchant $merchant): MerchantServiceContract
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function setByToken(string $token): MerchantServiceContract
    {
        $this->set(Merchant::where('token', $token)->first());

        return $this;
    }

    public function id(): string
    {
        return $this->merchant->id;
    }
}
