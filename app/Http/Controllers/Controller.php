<?php

namespace App\Http\Controllers;

use App\Actions\Actionable;
use App\Exceptions\Order\PolicyException;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Validation\ValidationException;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use ValidatesRequests;

    /**
     * @param  Actionable  $action
     * @return mixed
     * @throws ValidationException
     * @throws PolicyException
     * @throws Exception
     */
    protected function action(Actionable $action): mixed
    {
        $action->trigger();

        return $action->target();
    }
}
