<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Language;
use App\TranslatedLine;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProgressAPITest extends TestCase
{
    use DatabaseTransactions;

    /** @test */
    public function testThatProgressForUsersIsReportedCorrectly()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $language = factory(Language::class)->create();

        $user->languages()->attach($language->id);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $empty->fill(['value' => null])->save();

        $response = $this->get('/web/progress/user/'.$user->id);
        $response->assertSuccessful();

        // Should equal 50% translation
        $this->assertEquals(50, $response->json());
    }

    /** @test */
    public function testThatProgressIsAccessibleToAdmins()
    {
        $admin = $this->makeAdminUser();
        $user = $this->makeUser();
        $this->actingAs($admin);

        $language = factory(Language::class)->create();

        $user->languages()->attach($language->id);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
            'value' => 'with value',
        ]);

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
        ]);
        $empty->fill(['value' => null])->save();

        $response = $this->get('/web/progress/user/'.$user->id);
        $response->assertSuccessful();

        // Should equal 50% translation
        $this->assertEquals(50, $response->json());
    }

    /** @test */
    public function testThatProgressOfOtherUsersIsNotAccessibleToNonAdmins()
    {
        $user2 = $this->makeUser();
        $user = $this->makeUser();
        $this->actingAs($user2);

        $response = $this->get('/web/progress/user/'.$user->id);
        $response->assertStatus(403);
    }

    /** @test */
    public function testThatTheAuthenticatedUserGetsTheirOwnProgressIfNoUserIsSpecified()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $language = factory(Language::class)->create();

        $user->languages()->attach($language->id);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $empty->fill(['value' => null])->save();

        $response = $this->get('/web/progress/user');
        $response->assertSuccessful();

        // Should equal 50% translation
        $this->assertEquals(50, $response->json());
    }

    /** @test */
    public function testThatUsersCanAccessLanguageProgressIfAssigned()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $language = factory(Language::class)->create();

        $user->languages()->attach($language->id);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $empty->fill(['value' => null])->save();

        $response = $this->get('/web/progress/language/'.$language->id);
        $response->assertSuccessful();

        // Should equal 50% translation
        $this->assertEquals(50, $response->json()['progress']);
    }

    /** @test */
    public function testThatAdminsCanAccessLanguageProgressEvenIfNotAssigned()
    {
        $user = $this->makeAdminUser();
        $this->actingAs($user);

        $language = factory(Language::class)->create();

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
            'is_current' => true,
            'needs_updating' => false,
        ]);
        $empty->fill(['value' => null])->save();

        $response = $this->get('/web/progress/language/'.$language->id);
        $response->assertSuccessful();

        // Should equal 50% translation
        $this->assertEquals(50, $response->json()['progress']);
    }
}
