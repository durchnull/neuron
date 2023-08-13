<?php

namespace App\Providers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
    }

    public function boot(): void
    {
        // @todo [query]
        Model::preventLazyLoading(true);

        // @todo [database]
        Relation::morphMap([
            'neuron-inventory' => 'App\Models\Integration\Inventory\NeuronInventory',
            'weclapp' => 'App\Models\Integration\Inventory\Weclapp',
            'billbee' => 'App\Models\Integration\Inventory\Billbee',
        ], true);
        
        if (false) {
            File::append(
                storage_path('/logs/query.log'),
                request()->url() . PHP_EOL . PHP_EOL
            );

            DB::listen(function ($query) {
                File::append(
                    storage_path('/logs/query.log'),
                    $query->sql
                    . PHP_EOL
                    . PHP_EOL
                );
            });
        }
    }
}
