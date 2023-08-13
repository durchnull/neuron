<?php

namespace Tests\Feature\Action\Engine\Merchant;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class CreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws ValidationException
     * @throws Exception
     */
    public function test_create_merchant(): void
    {
        $merchant = $this->actionMerchantCreate();

        $this->assertTrue($merchant->exists);
        $this->assertDatabaseCount('merchants', 1);
    }
}
