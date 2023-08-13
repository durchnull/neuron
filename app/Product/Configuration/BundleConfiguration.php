<?php

namespace App\Product\Configuration;

use Illuminate\Contracts\Support\Arrayable;

class BundleConfiguration implements Arrayable
{
    protected array $groups;

    public function __construct()
    {
        $this->groups = [];
    }

    public static function make(): BundleConfiguration
    {
        return new static();
    }

    public function addGroup(BundleConfigurationGroup $bundleConfigurationGroup): BundleConfiguration
    {
        $this->groups[] = $bundleConfigurationGroup;

        return $this;
    }

    public function toArray()
    {
        return array_map(
            fn(BundleConfigurationGroup $bundleConfigurationGroup) => $bundleConfigurationGroup->toArray(),
            $this->groups
        );
    }
}
