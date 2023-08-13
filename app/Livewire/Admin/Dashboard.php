<?php

namespace App\Livewire\Admin;

use App\Enums\Order\OrderStatusEnum;
use App\Facades\SalesChannel;
use App\Models\Engine\Order;
use App\Models\Engine\Coupon;
use App\Models\Engine\Customer;
use App\Models\Engine\Product;
use App\Models\Engine\ProductPrice;
use App\Models\Engine\Stock;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class Dashboard extends Component
{
    public int $orderRevenue;

    public int $cartRevenue;

    public int $orderCount;

    public int $cartCount;

    public int $cartActiveTodayCount;

    public int $productCount;
    public int $customerCount;

    public int $newCustomerCount;
    public int $couponCount;

    public int $stockQueueSum;

    public int $actionRuleCount;

    public Collection $cartRules;

    public array $integrations;

    public array $timelineEvents;

    public function mount()
    {
        $this->orderRevenue = Order::where('sales_channel_id', SalesChannel::id())->whereIn('status', [
            OrderStatusEnum::Accepted,
            OrderStatusEnum::Confirmed,
            OrderStatusEnum::Shipped,
        ])->sum('amount');

        $this->cartRevenue = Order::where('sales_channel_id', SalesChannel::id())->whereIn('status', [
            OrderStatusEnum::Open,
            OrderStatusEnum::Placing,
        ])->sum('amount');

        $this->orderCount = Order::where('sales_channel_id', SalesChannel::id())->whereNotIn('status', [
            OrderStatusEnum::Open,
            OrderStatusEnum::Placing,
        ])->count();

        $this->cartCount = Order::where('sales_channel_id', SalesChannel::id())->whereIn('status', [
            OrderStatusEnum::Open,
            OrderStatusEnum::Placing,
        ])->count();

        $this->cartActiveTodayCount = Order::where('sales_channel_id', SalesChannel::id())->whereIn('status', [
            OrderStatusEnum::Open,
            OrderStatusEnum::Placing,
        ])->where('updated_at', '>=', now()->setTime(0, 0, 0, 0))->count();

        $this->productCount = Product::where('sales_channel_id', SalesChannel::id())->count();
        $this->customerCount = Customer::where('sales_channel_id', SalesChannel::id())->count();
        $this->newCustomerCount = Customer::where([
            'sales_channel_id' => SalesChannel::id(),
            'new' => true
        ])->count();
        $this->couponCount = Coupon::where([
            'sales_channel_id' => SalesChannel::id(),
        ])->count();

        $this->cartRules = \App\Models\Engine\CartRule::where([
            'sales_channel_id' => SalesChannel::id(),
        ])->get();

        $this->stockQueueSum = Stock::whereHas('product', function (Builder $query) {
            $query->where('sales_channel_id', SalesChannel::id());
        })->sum('queue');

        $this->actionRuleCount = \App\Models\Engine\ActionRule::where([
            'sales_channel_id' => SalesChannel::id(),
        ])->count();

        $this->integrations = \App\Facades\Integrations::getModels([])->toArray();

        $this->timelineEvents = array_merge(
            Product::with('prices.product')
                ->get()
                ->pluck('prices')
                ->flatten()
                ->map(fn(ProductPrice $productPrice) => [
                    'begin' => $productPrice->begin_at,
                    'end' => $productPrice->end_at,
                    'title' => $productPrice->product->name . ' for ' . number_format($productPrice->net_price / 100, 2, ',', '.') . ' ' .  SalesChannel::get()->currency_code
                ])
                ->toArray(),
            // @todo add cartRules and actionRules where condition contains propertyType date
        );
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}
