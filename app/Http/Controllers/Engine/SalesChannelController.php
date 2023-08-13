<?php

namespace App\Http\Controllers\Engine;

use App\Http\Resources\Engine\SalesChannelResource;
use App\Models\Engine\SalesChannel;

class SalesChannelController extends EngineResourceController
{
    public static function getModelClass(): string
    {
        return SalesChannel::class;
    }

    public static function getResourceClass(): string
    {
        return SalesChannelResource::class;
    }
}
