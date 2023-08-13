<?php

namespace App\View\Components\Admin;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Sidebar extends Component
{
    public array $items;

    public function __construct()
    {
        $this->items = [
            [
                [
                    'route' => 'admin.dashboard',
                    'icon' => 'rectangle-group',
                    'title' => 'Dashboard'
                ]
            ],
            [
                [
                    'route' => 'admin.carts',
                    'icon' => 'shopping-cart',
                    'title' => 'Carts'
                ],
                [
                    'route' => 'admin.orders',
                    'icon' => 'shopping-bag',
                    'title' => 'Orders'
                ],
                [
                    'route' => 'admin.products',
                    'icon' => 'tag',
                    'title' => 'Products'
                ],
                [
                    'route' => 'admin.customers',
                    'icon' => 'users',
                    'title' => 'Customers'
                ],
            ],
            [
                [
                    'route' => 'admin.coupons',
                    'icon' => 'gift',
                    'title' => 'Coupons'
                ],
                [
                    'route' => 'admin.cart-rules',
                    'icon' => 'arrow-trending-up',
                    'title' => 'Promotions'
                ],
                [
                    'route' => 'admin.action-rules',
                    'icon' => 'shield',
                    'title' => 'Policies'
                ],
            ],
            [
                [
                    'route' => 'admin.shippings',
                    'icon' => 'truck',
                    'title' => 'Shippings'
                ],
                [
                    'route' => 'admin.payments',
                    'icon' => 'credit-card',
                    'title' => 'Payments'
                ],
                [
                    'route' => 'admin.integration',
                    'icon' => 'puzzle-piece',
                    'title' => 'Integrations'
                ],
            ],
            [
                [
                    'route' => 'admin.sales-channels',
                    'icon' => 'building-storefront',
                    'title' => 'Sales Channels'
                ],
            ],
            'user',
        ];
    }

    public function render(): View|Closure|string
    {
        return view('components.blocks.sidebar');
    }
}
