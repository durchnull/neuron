<?php

namespace App\Livewire\Admin\List;

use App\Models\Engine\Coupon;
use Illuminate\Database\Eloquent\Builder;

class Coupons extends View
{
    public array $search = [
        'name',
        'code'
    ];

    public array $tableAttributes = [
        'enabled' => 'status',
        'code' => 'string',
        'name' => 'string',
        'rule' => 'rule',
        'combinable' => 'status',
    ];

    public function getBuilder(): Builder
    {
        return Coupon::with('rule');
    }
}
