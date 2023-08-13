<?php

namespace App\Http\Response\Webhook;

use Illuminate\Http\JsonResponse;

class WebhookTransactionSuccessResponse extends JsonResponse
{
    public function __construct($data = null, $status = 200, $headers = [], $options = 0, $json = false)
    {
        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers, $json);
    }
}
