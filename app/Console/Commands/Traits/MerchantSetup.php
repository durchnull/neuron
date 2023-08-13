<?php

namespace App\Console\Commands\Traits;

use App\Actions\Engine\ActionRule\ActionRuleCreateAction;
use App\Actions\Engine\CartRule\CartRuleCreateAction;
use App\Actions\Engine\Condition\ConditionCreateAction;
use App\Actions\Engine\Coupon\CouponCreateAction;
use App\Actions\Engine\Customer\CustomerCreateAction;
use App\Actions\Engine\Merchant\MerchantCreateAction;
use App\Actions\Engine\Order\OrderCreateAction;
use App\Actions\Engine\Payment\PaymentCreateAction;
use App\Actions\Engine\Product\ProductCreateAction;
use App\Actions\Engine\Rule\RuleCreateAction;
use App\Actions\Engine\SalesChannel\SalesChannelCreateAction;
use App\Actions\Engine\Shipping\ShippingCreateAction;
use App\Actions\Engine\Vat\VatCreateAction;
use App\Actions\Integration\Inventory\Billbee\BillbeeCreateAction;
use App\Actions\Integration\Inventory\NeuronInventory\NeuronInventoryCreateAction;
use App\Actions\Integration\Inventory\Weclapp\WeclappCreateAction;
use App\Actions\Integration\Mail\Mailgun\MailgunCreateAction;
use App\Actions\Integration\Marketing\Klicktipp\KlicktippCreateAction;
use App\Actions\Integration\PaymentProvider\AmazonPay\AmazonPayCreateAction;
use App\Actions\Integration\PaymentProvider\Mollie\MollieCreateAction;
use App\Actions\Integration\PaymentProvider\NeuronPayment\NeuronPaymentCreateAction;
use App\Actions\Integration\PaymentProvider\PostFinance\PostFinanceCreateAction;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\SalesChannel;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

trait MerchantSetup
{
    protected array $merchantSetupTriggeredActions = [];

    public function merchantSetupInit(): void
    {
        $this->merchantSetupTriggeredActions = [];

        foreach (glob(storage_path('logs/*.log')) as $logFile) {
            File::delete($logFile);
        }

        $this->call('cache:clear');

        Session::forget('admin');
        Session::forget('admin.order_id');
        Session::forget('admin.sales_channel_id');
        Session::forget('admin.sales_channel_token');
        Session::forget('admin.sales_channel_cart_token');

        $this->call('migrate:fresh');
    }


    public function prepareStoreProject(SalesChannel $salesChannel): void
    {
        $apiUrl = 'https://' . $salesChannel->domains[0];
        $apiToken = $salesChannel->cart_token;
        $shopUrl = route('shop', ['id' => $salesChannel->id]);

        File::put(
            base_path('.env.store'),
            'API_URL="' . $apiUrl . '"' . PHP_EOL .
            'API_TOKEN="' . $apiToken . '"' . PHP_EOL .
            'SHOP_URL="' . $shopUrl . '"'
        );
    }

    public function merchantSetupSummary(): void
    {
        foreach ($this->merchantSetupTriggeredActions as $group => $actions) {
            foreach ($actions as $index => $_actions) {
                if (!empty(trim($index))) {
                    \Laravel\Prompts\info('');
                    \Laravel\Prompts\info(' 🚀️ ' . $index);
                    \Laravel\Prompts\info('');
                }

                foreach (array_count_values($_actions) as $action => $count) {
                    $_count = $count < 10 ? (' ' . $count) : $count;
                    $actionShortName = str_replace('CreateAction', '', class_basename($action));
                    $emoji = ' ';

                    switch ($action) {
                        case MerchantCreateAction::class:
                            $emoji = '🏛️ ';
                            break;
                        case SalesChannelCreateAction::class:
                            $emoji = '🚀️';
                            break;
                        case AmazonPayCreateAction::class:
                        case MollieCreateAction::class:
                        case NeuronPaymentCreateAction::class:
                        case PostFinanceCreateAction::class:
                            $emoji = '💰';
                            break;
                        case BillbeeCreateAction::class:
                        case NeuronInventoryCreateAction::class:
                        case WeclappCreateAction::class:
                            $emoji = '🚚';
                            break;
                        case MailgunCreateAction::class:
                            $emoji = '📫';
                            break;
                        case RuleCreateAction::class:
                        case ActionRuleCreateAction::class:
                        case CartRuleCreateAction::class:
                        case CouponCreateAction::class:
                        case CustomerCreateAction::class:
                        case ConditionCreateAction::class:
                        case ProductCreateAction::class:
                        case PaymentCreateAction::class:
                        case OrderCreateAction::class:
                        case ShippingCreateAction::class:
                        case VatCreateAction::class:
                            $emoji = '🛒';
                            break;
                        case KlicktippCreateAction::class:
                            $emoji = '📊';
                            break;
                    }
                    \Laravel\Prompts\info(
                        implode(' ', [
                            $_count,
                            $emoji,
                            $actionShortName,
                        ])
                    );
                }
            }
        }

        \Laravel\Prompts\outro('✔︎ Finished');
    }


    /**
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    public function merchantSetupAction(string $actionClass, Model $model, array $attributes): mixed
    {
        $action = new $actionClass(
            $model,
            $attributes,
            TriggerEnum::Admin
        );

        $action->trigger();

        $this->merchantSetupTriggeredActions[isset($attributes['sales_channel_id']) ? 'sales-channels' : 'other'][$attributes['sales_channel_id'] ?? ' '][] = $actionClass;

        return $action->target();
    }
}
