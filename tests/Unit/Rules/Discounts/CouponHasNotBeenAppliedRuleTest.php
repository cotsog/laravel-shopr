<?php

namespace Happypixels\Shopr\Tests\Unit\Rules\Discounts;

use Happypixels\Shopr\Tests\TestCase;
use Happypixels\Shopr\Tests\Support\Traits\InteractsWithCart;
use Happypixels\Shopr\Rules\Discounts\CouponHasNotBeenApplied;

class CouponHasNotBeenAppliedRuleTest extends TestCase
{
    use InteractsWithCart;

    /** @test */
    public function it_fails_if_coupon_has_been_applied()
    {
        $this->mockCart()->shouldReceive('hasDiscount')->with('TEST')->andReturn(true);

        $this->assertFalse((new CouponHasNotBeenApplied)->passes('code', 'TEST'));
    }

    /** @test */
    public function it_passes_if_coupon_has_not_been_applied()
    {
        $this->mockCart()->shouldReceive('hasDiscount')->with('TEST')->andReturn(false);

        $this->assertTrue((new CouponHasNotBeenApplied)->passes('code', 'TEST'));
    }
}
