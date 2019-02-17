<?php

namespace Tests\Unit;

use App\Services\NotificationRegistry;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class NotificationRegistryTest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function testThatListingWorks()
    {
        $registry = new NotificationRegistry();
        $notifications = $registry->find();
        $this->assertNotEmpty($notifications);
    }
}
