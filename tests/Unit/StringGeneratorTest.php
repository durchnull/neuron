<?php

namespace Tests\Unit;

use App\Enums\Generator\StringPattern;
use App\Generators\CouponCodeGenerator;
use App\Generators\OrderNumberGenerator;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class StringGeneratorTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @throws Exception
     */
    public function test_order_id_generation()
    {
        $generator = new OrderNumberGenerator(StringPattern::YearDashesNineAlphaNumericUpper);

        $orderId = $generator->generate();

        $this->assertEquals(strlen(StringPattern::YearDashesNineAlphaNumericUpper->value), strlen($orderId));
        $this->assertTrue(Str::startsWith($orderId, now()->format('Y')));
    }

    /**
     * @throws Exception
     */
    public function test_coupon_code_generation()
    {
        $generator = new CouponCodeGenerator(StringPattern::NineAlphaNumericUpper);

        $couponCode = $generator->generate();

        $this->assertEquals(strlen(StringPattern::NineAlphaNumericUpper->value), strlen($couponCode));
    }
}
