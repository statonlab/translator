<?php

namespace Tests\Feature;

use App\Language;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LanguagesAPITest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function testThatListingLanguagesWorks()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        factory(Language::class, 10)->create();

        $response = $this->get('/web/languages');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'language',
                    'users',
                ],
            ],
        ]);
    }

    /** @test */
    public function testThatNonAdminsCantAccessLanguages()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        factory(Language::class, 10)->create();

        $response = $this->get('/web/languages');

        $response->assertStatus(403);
    }

    /** @test */
    public function testThatOnlyAdminsCanMakeLanguages()
    {
        $user = $this->makeUser();

        $this->actingAs($user);

        $this->post('/web/languages', [
            'language' => 'English',
            'language_code' => 'en-US',
            'image' => '/img/test.png',
        ])->assertStatus(403);
    }

    /** @test */
    public function testThatCreatingLanguagesWorks()
    {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $response = $this->post('/web/languages', [
            'language' => 'English',
            'language_code' => 'en-US',
            'image' => '/img/test.png',
        ]);

        $response->assertSuccessful();
        $response->assertJsonStructure([
            'language',
            'language_code',
            'image',
        ]);
    }

    public function testThatDeletingLanguagesWorks() {
        $user = $this->makeAdminUser();

        $this->actingAs($user);

        $language = factory(Language::class)->create();

        $response = $this->delete("/web/languages/$language->id");

        $response->assertSuccessful();
    }
}
