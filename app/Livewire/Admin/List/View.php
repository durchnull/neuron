<?php

namespace App\Livewire\Admin\List;

use App\Facades\SalesChannel;
use Illuminate\Database\Eloquent\Builder;
use Livewire\Component;
use Livewire\WithPagination;

abstract class View extends Component
{
    use WithPagination;

    public string $query = '';

    public string $resourceRoute = 'admin';

    public array $search = [
        'id'
    ];

    public array $tableAttributes = [
        'id' => 'string',
    ];

    abstract public function getBuilder(): Builder;

    public function render()
    {
        // SalesChannel::set(\App\Models\Engine\SalesChannel::find(Session::get('admin.sales_channel_id')));

        $this->resetPage();

        $builder = $this->getBuilder();

        if (!$this instanceof SalesChannels && !$this instanceof Customers) {
            $builder->where('sales_channel_id', SalesChannel::id());
        }

        if (!empty($query) && !empty($this->search)) {
            foreach ($this->search as $attribute) {
                $builder = $builder->where($attribute, 'like', '%' . $this->query . '%');
            }
        }

        return view('livewire.admin.list-view', [
            'models' => $builder->paginate(10),
            'headline' => class_basename($this),
            'resourceRoute' => $this->resourceRoute
        ]);
    }
}
