<?php

namespace Tests\Feature\Api\Engine\Merchant;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_merchant_create(): void
    {
        $this->markTestSkipped('Merchants have no api yet');
    }
}
