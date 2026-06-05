<?php

namespace Tests\Unit;

use App\Services\Prosa\ProsaResultCode;
use PHPUnit\Framework\TestCase;

class ProsaResultCodeTest extends TestCase
{
    public function test_successful_codes_are_approved(): void
    {
        foreach (['000.000.000', '000.100.110', '000.300.000', '000.600.000'] as $code) {
            $this->assertTrue(ProsaResultCode::isApproved($code), "expected approved: {$code}");
            $this->assertFalse(ProsaResultCode::isRejected($code), "should not be rejected: {$code}");
        }
    }

    public function test_pending_codes_are_pending(): void
    {
        foreach (['000.200.000', '000.200.100', '800.400.500', '100.400.500'] as $code) {
            $this->assertTrue(ProsaResultCode::isPending($code), "expected pending: {$code}");
            $this->assertFalse(ProsaResultCode::isRejected($code), "pending is not rejected: {$code}");
        }
    }

    public function test_declined_codes_are_rejected(): void
    {
        foreach (['800.100.151', '800.100.100', '800.800.102', '900.100.300'] as $code) {
            $this->assertTrue(ProsaResultCode::isRejected($code), "expected rejected: {$code}");
            $this->assertFalse(ProsaResultCode::isApproved($code), "rejected is not approved: {$code}");
        }
    }

    public function test_manual_review_is_treated_as_approved(): void
    {
        $this->assertTrue(ProsaResultCode::isManualReview('000.400.000'));
        $this->assertTrue(ProsaResultCode::isApproved('000.400.000'));
    }
}
