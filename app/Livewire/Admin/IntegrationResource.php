<?php

namespace App\Livewire\Admin;

use App\Models\Engine\Transaction;
use Livewire\Component;

class IntegrationResource extends Component
{
    public array $resource;

    public array $customerProfile;

    public function mount(string $id)
    {
        /** @var Transaction $transaction */
        $transaction = Transaction::with(['order.payment'])
            ->where('resource_id', $id)
            ->first();

        $paymentProvider = \App\Facades\Integrations::getPaymentProvider($transaction->integration);
        $resource = $paymentProvider->getResource($transaction->resource_id);

        $_resource = $resource->getResource();
        $this->resource = is_array($_resource) ? $this->filterNullValues($_resource) : json_decode(json_encode($_resource), true);
        $this->customerProfile = $resource->getCustomerProfile()
            ? $this->filterNullValues(json_decode(json_encode($resource->getCustomerProfile()), true))
            : [];
    }

    public function render()
    {
        return view('livewire.admin.integration-resource');
    }

    // @todo
    protected function filterNullValues(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = $this->filterNullValues($value);
            }

            if ($array[$key] === null || (is_array($array[$key]) && empty($array[$key]))) {
                unset($array[$key]);
            }
        }

        return $array;
    }
}
