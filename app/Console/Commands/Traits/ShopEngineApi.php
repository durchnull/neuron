<?php

namespace App\Console\Commands\Traits;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

trait ShopEngineApi
{
    protected array $shopengineSettings;

    private function shopengineGet(string $resource, array $parameter = [])
    {
        return \Laravel\Prompts\spin(
            fn () => $this->makeRequest('GET', $resource, $parameter, []),
            $resource
        );
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    private function makeRequest(
        string $method,
        string $resource,
        array $parameter,
        array $postParameter = [],
    ) {
        foreach ($parameter as $key => $value) {
            if ($value === '') {
                return [];
            }
        }

        $timestamp = time();
        $signature = $this->makeSignature(http_build_query($parameter + $postParameter), $timestamp);

        $client = new GuzzleClient();

        $requestQuery = http_build_query(
            array_merge(
                $parameter,
                ['timestamp' => $timestamp, 'signature' => $signature]
            )
        );

        $url = $this->shopengineSettings['url'] . '/v2' . "/{$this->shopengineSettings['shop']}/$resource?$requestQuery";

        $response = $client->request(
            $method,
            $url,
            $method !== 'GET' ? ['json' => $postParameter] : []
        );

        $body = json_decode($response->getBody());

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception($response->getBody() . '', 10);
        }

        if (is_array($body)) {
            $array = [];
            foreach ($body as $c) {
                $array[] = $c;
            }
            return $array;
        } else {
            return $body;
        }
    }

    private function makeSignature($query, $timestamp): string
    {
        return base64_encode(hash('sha256', $query . $this->shopengineSettings['key'] . $timestamp));
    }
}
