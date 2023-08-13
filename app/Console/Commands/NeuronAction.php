<?php

namespace App\Console\Commands;

use App\Enums\Order\OrderStatusEnum;
use App\Enums\Transaction\TransactionStatusEnum;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Models\Engine\ActionRule;
use App\Models\Engine\Address;
use App\Models\Engine\Customer;
use App\Models\Engine\Order;
use App\Models\Engine\CartRule;
use App\Models\Engine\Condition;
use App\Models\Engine\Coupon;
use App\Models\Engine\Merchant;
use App\Models\Engine\Payment;
use App\Models\Engine\Product;
use App\Models\Engine\Rule;
use App\Models\Engine\SalesChannel;
use App\Models\Engine\Shipping;
use App\Models\Engine\Stock;
use App\Models\Engine\Transaction;
use App\Models\Engine\Vat;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use mysql_xdevapi\Exception;
use ReflectionClass;
use ReflectionException;

use function Laravel\Prompts\search;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\text;

class NeuronAction extends Command
{
    protected $signature = 'neuron:action';

    protected $description = 'Perform resource actions';

    protected array $attributeMap = [
        'action_rule' => ActionRule::class,
        'address' => Address::class,
        'cart_rule' => CartRule::class,
        'condition' => Condition::class,
        'coupon' => Coupon::class,
        'customer' => Customer::class,
        'merchant' => Merchant::class,
        'order' => Order::class,
        'payment' => Payment::class,
        'product' => Product::class,
        'rule' => Rule::class,
        'sales_channel' => SalesChannel::class,
        'shipping' => Shipping::class,
        'stock' => Stock::class,
        'transaction' => Transaction::class,
        'vat' => Vat::class,
    ];

    protected array $hasNameAttribute = [
        Merchant::class,
        SalesChannel::class,
        Condition::class,
        Shipping::class,
        Payment::class,
        Rule::class,
        Coupon::class,
        ActionRule::class,
        CartRule::class,
        Product::class,
    ];

    /**
     * @throws ReflectionException
     */
    public function handle()
    {
        $resources = $this->getResourcesAndActions();

        $merchantId = select(
            label: 'Which merchant?',
            options: Merchant::pluck('name', 'id'),
        );

        $salesChannelId = select(
            label: 'Which sales channel?',
            options: SalesChannel::where('merchant_id', $merchantId)
                ->pluck('name', 'id'),
        );

        \App\Facades\SalesChannel::set(SalesChannel::find($salesChannelId));

        $resource = select(
            'Select resource',
            array_keys($resources),
            Order::class,
            count($resources)
        );

        $actionClass = select(
            'Select action',
            $resources[$resource],
            null,
            count($resources[$resource])
        );

        if (Str::contains($actionClass, 'Create')) {
            /** @var Model $model */
            $model = new $resource();
        } else {
            $pluck = ['id'];

            if (in_array($resource, $this->hasNameAttribute)) {
                $pluck = ['name', 'id'];
            }

            $modelId = select(
                label: 'Which ' . class_basename($resource),
                options: $resource::where('sales_channel_id', $salesChannelId)
                    ->limit(50)
                    ->pluck(...$pluck),
            );
            $model = $resource::find($modelId);
        }

        $this->tableModel($model);

        $attributes = [];

        \Laravel\Prompts\info('Action input');

        foreach ($actionClass::rules() as $key => $rules) {
            if (Str::contains($key, '*')) {
                continue;
            }

            $attributes[$key] = $this->getAttribute($key, $rules, $salesChannelId, $model);
        }

        try {
            $action = new $actionClass($model, $attributes, TriggerEnum::App);
            $action->trigger();
            $model = $action->target();
        } catch (PolicyException $exception) {
            $this->error($exception->getMessage());

            return Command::FAILURE;
        }

        $this->tableModel($model);

        return Command::SUCCESS;
    }

    /**
     * @throws ReflectionException
     */
    public function tableModel(Model $model): void
    {
        $tableAttributes = [];

        foreach (array_keys($model->getAttributes()) as $_attribute) {
            $attribute = $model->{$_attribute};

            if (is_object($attribute) && (new ReflectionClass($attribute))->isEnum()) {
                $attribute = $attribute->value;
            }

            if (is_string($attribute) ||
                is_numeric($attribute)
            ) {
                $tableAttributes[$_attribute] = $attribute;
            }
        }

        \Laravel\Prompts\info(class_basename($model));

        table(
            array_keys($tableAttributes),
            [$tableAttributes]
        );
    }

    /**
     * @throws ReflectionException
     */
    public function getResourcesAndActions(): array
    {
        $resources = [];

        foreach (glob(app_path('/Actions/Engine/*/*.php')) as $actionFile) {
            $actionClass = 'App\\' . Str::before(Str::after($actionFile, 'app/'), '.php');
            $actionClass = str_replace('/', '\\', $actionClass);

            $class = new ReflectionClass($actionClass);

            if (!$class->isAbstract()) {
                if (! isset($resources[$actionClass::targetClass()])) {
                    $resources[$actionClass::targetClass()] = [$actionClass];
                } else {
                    $resources[$actionClass::targetClass()][] = $actionClass;
                }
            }
        }

        return $resources;
    }


    protected function getAttribute(string $key, mixed $rules, string $salesChannelId, Model $model): mixed
    {
        if (is_array($rules)) {
            $required = in_array('required', $rules);
            $isNumber = in_array('numeric', $rules) || in_array('integer', $rules);
            $isUuid = in_array('uuid', $rules);
        } elseif(is_string($rules)) {
            $required = Str::contains($rules, 'required');
            $isNumber = Str::contains($rules, ['|integer', '|numeric']);
            $isUuid = Str::contains($rules, ['|uuid']);
        }

        $isResourceId = Str::endsWith($key, '_id');
        $isStatus = $key === 'status';

        $requiredLabel = $required ? ' (*)' : ' (optional)';

        if ($key === 'sales_channel_id') {
            return $salesChannelId;
        } elseif ($key === 'configuration') {
            return null;
        } elseif ($isNumber) {
            return text(
                label: $key . $requiredLabel,
                required: $required,
                validate: fn(string $value) => match (true) {
                    !is_numeric($value) => 'The value must be numeric',
                    default => null
                }
            );
        }
        elseif ($isStatus) {
            $options = match (get_class($model)) {
                Order::class => collect(OrderStatusEnum::cases())->pluck('value', 'value'),
                Transaction::class => collect(TransactionStatusEnum::cases())->pluck('value', 'value'),
                default => throw new Exception('Not implemented enum for ' . get_class($model)),
            };

            return select(
                label: 'Status',
                options: $options,
                required: $required,
                scroll: count($options)
            );
        }
        elseif ($isResourceId) {
            /** @var Model $modelClass */
            $modelClass = $this->attributeMap[Str::before($key, '_id')];

            $pluck = ['id'];

            if (in_array($modelClass, $this->hasNameAttribute)) {
                $pluck = ['name', 'id'];
            }

            if (in_array('name', $pluck)) {
                return search(
                    'Search for ' . class_basename($modelClass) . $requiredLabel,
                    fn (string $value) => $modelClass::where('sales_channel_id', $salesChannelId)
                        ->where('name', 'like', "%{$value}%")
                        ->pluck('name', 'id')
                        ->toArray(),
                    required: $required
                );
            }

            return select(
                label: 'Which ' . class_basename($modelClass) . '?' . $requiredLabel,
                options: $modelClass::where('sales_channel_id', $salesChannelId)
                    ->pluck(...$pluck),
                required: $required
            );
        }

        return text(
            label: $key . $requiredLabel,
            required: $required
        );
    }
}
