<?php

namespace App\Http\Middleware;

use App\Facades\SalesChannel;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $salesChannel = \App\Models\Engine\SalesChannel::find(Session::get('admin.sales_channel_id'));

        if (!$salesChannel) {
            $salesChannel = \App\Models\Engine\SalesChannel::find($request->user()->merchant->salesChannels->first()->id);
        }

        SalesChannel::set($salesChannel);

        return $next($request);
    }
}
