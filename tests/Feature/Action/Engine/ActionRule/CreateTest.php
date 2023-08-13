<?php

namespace Tests\Feature\Action\Engine\ActionRule;

use App\Actions\Engine\Order\OrderAddItemAction;
use App\Condition\Presets\MaxActionProductQuantity;
use App\Facades\SalesChannel;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_create_action_rule(): void
    {
        $merchant = $this->actionMerchantCreate();
        $salesChannel = $this->actionSalesChannelCreate($merchant->id);
        SalesChannel::set($salesChannel);

        $neuronInventory = $this->actionNeuronInventoryCreate($salesChannel->id);
        $product = $this->actionProductCreate($salesChannel->id, $neuronInventory->id, get_class($neuronInventory));
        $condition = $this->actionConditionCreate($salesChannel->id, [
            'name' => MaxActionProductQuantity::name($product->name, 10),
            'collection' => MaxActionProductQuantity::make($product->id, 10)->toArray()
        ]);
        $actionRule = $this->actionActionRuleCreate($salesChannel->id, $condition->id, [
            'name' => 'Max 10 ' . $product->name,
            'action' => class_basename(OrderAddItemAction::class),
            'enabled' => true,
        ]);

        $this->assertTrue($actionRule->exists);
        $this->assertDatabaseCount('action_rules', 1);
    }
}
