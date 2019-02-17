<?php

namespace Tests\Feature;

use App\NotificationType;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationsAPITest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function testThatUsersCanListTheirSubscriptions()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        factory(NotificationType::class)->create();

        $response = $this->get('/web/subscriptions');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id',
                'title',
                'subscribed',
            ],
        ]);
    }

    /** @test */
    public function testThatUserSubscriptionsAreListedInResponse()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $subscribed_to = factory(NotificationType::class)->create();
        $user->notificationTypes()->attach($subscribed_to);

        $response = $this->get('/web/subscriptions');
        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'id',
                'title',
                'subscribed',
            ],
        ]);

        $notifications = $response->json();

        // check that this particular notification is listed
        $found = false;
        $subscribed = false;
        foreach ($notifications as $notification) {
            if ($notification['id'] === $subscribed_to->id) {
                $found = true;
                $subscribed = $notification['subscribed'];
            }
        }

        $this->assertTrue($found);
        $this->assertTrue($subscribed);
    }

    /** @test */
    public function testThatUserCanToggleNotifications()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $notification = factory(NotificationType::class)->create();

        // Toggle to true
        $response = $this->post("/web/subscription/$notification->id");
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'subscribed',
        ]);

        $this->assertTrue($response->json()['subscribed']);


        // Toggle to false
        $response = $this->post("/web/subscription/$notification->id");
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'subscribed',
        ]);
        $this->assertFalse($response->json()['subscribed']);
    }
}
