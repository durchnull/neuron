<?php

namespace App\Http\Response\Webhook;

use Illuminate\Http\JsonResponse;

class WebhookTransactionFailResponse extends JsonResponse
{
    public function __construct($data = null, $status = 400, $headers = [], $options = 0, $json = false)
    {
        $this->encodingOptions = $options;

        parent::__construct($data, $status, $headers, $json);
    }
}
