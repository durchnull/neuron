<?php

namespace Tests\Unit;

use App\Models\Engine\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RuleConsequenceTest extends TestCase
{
    use RefreshDatabase;

    public function test_rule_consequences()
    {
        $rule = Rule::factory()
            ->create();

        $this->assertEquals(
            json_encode($rule->consequences->toArray()),
            json_encode(Rule::all()->first()->consequences->toArray())
        );
    }
}
