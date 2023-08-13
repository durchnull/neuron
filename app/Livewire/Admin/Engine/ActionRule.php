<?php

namespace App\Livewire\Admin\Engine;

use App\Actions\Engine\ActionRule\ActionRuleDeleteAction;
use App\Actions\Engine\Order\OrderRefundAction;
use App\Enums\TriggerEnum;
use App\Exceptions\Order\PolicyException;
use App\Facades\SalesChannel;
use Exception;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Rule;
use Livewire\Component;

class ActionRule extends Component
{
    #[Rule('required|string')]
    public string $name;

    public bool $canDelete;

    public \App\Models\Engine\ActionRule $actionRule;


    public function mount(string $id)
    {
        /** @var \App\Models\Engine\ActionRule $actionRule */
        $actionRule = \App\Models\Engine\ActionRule::find($id);

        $this->actionRule = $actionRule;
        $this->name = $actionRule->name;

        $this->canDelete = true;
    }

    /**
     * @throws Exception
     */
    public function delete()
    {
        SalesChannel::set($this->actionRule->salesChannel);

        $action = new ActionRuleDeleteAction($this->actionRule, [], TriggerEnum::Admin);
        $action->trigger();

        $this->redirect(route('admin.action-rules'));
    }

    public function render()
    {
        return view('livewire.admin.engine.action-rule');
    }
}
