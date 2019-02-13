<?php

namespace Tests\Feature;

use App\Language;
use App\Platform;
use App\TranslatedLine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\File;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationsAPITest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Add json header.
     */
    public function setUp()
    {
        parent::setUp();

        $this->withHeader('Accept', 'application/json');
    }

    /** @test */
    public function testThatAdminsGetAllLanguages()
    {
        $admin = $this->makeAdminUser();
        $this->actingAs($admin);

        $platform = factory(Platform::class)->create();
        $languages = factory(Language::class,
            10)->create(['platform_id' => $platform->id]);

        $assigned = $languages->take(5);
        $admin->languages()->sync($assigned);

        $response = $this->get("/web/translation/languages/{$platform->id}");

        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'language',
                'id',
            ],
        ]);

        $this->assertGreaterThanOrEqual(10, count($response->json()));
    }

    /** @test */
    public function testThatNonAdminsGetOnlyAssignedLanguages()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $platform = factory(Platform::class)->create();
        $languages = factory(Language::class,
            10)->create(['platform_id' => $platform->id]);
        $assigned = $languages->take(5);
        $user->languages()->sync($assigned);

        $response = $this->get("/web/translation/languages/{$platform->id}");

        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'language',
                'id',
            ],
        ]);

        $this->assertEquals(5, count($response->json()));
    }

    /** @test */
    public function testThatGuestsCantAccessLanguagesAPI()
    {
        $platform = factory(Platform::class)->create();

        $this->get("/web/translation/languages/{$platform->id}")->assertStatus(401);
    }

    /** @test */
    public function testThatGuestsCantAccessLinesAPI()
    {
        $platform = factory(Platform::class)->create();

        $this->get('/web/translation/lines/'.$platform->id)->assertStatus(401);
    }

    /** @test */
    public function testThatAdminsCanListLines()
    {
        $this->actingAs($this->makeAdminUser());

        $platform = factory(Platform::class)->create();

        $file = factory(File::class)->create([
            'platform_id' => $platform->id,
            'is_current' => true,
        ]);

        $language = factory(Language::class)->create(['platform_id' => $platform->id]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $language->id,
        ]);

        $response = $this->get('/web/translation/lines/'.$language->id);

        $response->assertSuccessful()->assertJsonStructure([
            'data' => [
                [
                    'serialized_line' => [
                        'value',
                    ],
                    'language' => [
                        'language',
                    ],
                    'key',
                    'value',
                ],
            ],
        ]);
    }

    /** @test */
    public function testThatUsersCanListLinesOfAssignedLanguages()
    {
        /** @var \App\User $user */
        $user = $this->makeUser();

        $this->actingAs($user);

        /** @var Platform $platform */
        $platform = factory(Platform::class)->create();

        /** @var File $file */
        $file = factory(File::class)->create([
            'platform_id' => $platform->id,
            'is_current' => true,
        ]);

        /** @var TranslatedLine $assigned_language */
        $assigned_language = factory(Language::class)->create([
            'platform_id' => $platform->id,
        ]);

        /** @var TranslatedLine $unassigned_language */
        $unassigned_language = factory(Language::class)->create([
            'platform_id' => $platform->id,
        ]);

        $user->languages()->attach([$assigned_language->id]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $assigned_language->id,
        ]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $unassigned_language->id,
        ]);

        $response = $this->get('/web/translation/lines/'.$assigned_language->id);

        $response->assertSuccessful()->assertJsonStructure([
            'data' => [
                [
                    'serialized_line' => [
                        'value',
                    ],
                    'language' => [
                        'language',
                    ],
                    'key',
                    'value',
                ],
            ],
        ]);

        $response = $this->get('/web/translation/lines/'.$unassigned_language->id);
        $response->assertStatus(403);
    }

    /** @test */
    public function testThatAdminsCanTranslateLines()
    {
        $this->actingAs($this->makeAdminUser());

        $platform = factory(Platform::class)->create();

        $file = factory(File::class)->create([
            'platform_id' => $platform->id,
            'is_current' => true,
        ]);

        $line = factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
        ]);

        $response = $this->put("/web/translation/line/{$line->id}", [
            'value' => 'test',
        ]);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'serialized_line' => [
                'key',
                'value',
            ],
            'language' => [
                'language',
            ],
            'key',
            'value',
            'user' => [
                'name'
            ]
        ]);
    }

    /** @test */
    public function testThatUsersCanTranslateAssignedLines()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $platform = factory(Platform::class)->create();

        $file = factory(File::class)->create([
            'platform_id' => $platform->id,
            'is_current' => true,
        ]);

        /** @var TranslatedLine $line */
        $line = factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
        ]);

        $user->languages()->syncWithoutDetaching([$line->language->id]);
        $user->platforms()->syncWithoutDetaching([$platform->id]);

        $response = $this->put("/web/translation/line/{$line->id}", [
            'value' => 'test',
        ]);
        $response->assertSuccessful();
        $response->assertJsonStructure([
            'id',
            'serialized_line' => [
                'id',
                'key',
                'value',
            ],
            'language' => [
                'id',
                'language',
            ],
            'key',
            'value',
            'user' => [
                'id',
                'name',
            ],
        ]);

        $this->assertEquals('test', $response->json()['value']);
    }
}
