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

        $languages = factory(Language::class, 10)->create();
        $assigned = $languages->take(5);
        $admin->languages()->sync($assigned);

        $response = $this->get('/web/translation/languages');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'label',
                'value',
            ],
        ]);

        $this->assertGreaterThanOrEqual(10, count($response->json()));
    }

    /** @test */
    public function testThatNonAdminsGetOnlyAssignedLanguages()
    {
        $user = $this->makeUser();
        $this->actingAs($user);

        $languages = factory(Language::class, 10)->create();
        $assigned = $languages->take(5);
        $user->languages()->sync($assigned);

        $response = $this->get('/web/translation/languages');

        $response->assertSuccessful();
        $response->assertJsonStructure([
            [
                'label',
                'value',
            ],
        ]);

        $this->assertEquals(5, count($response->json()));
    }

    /** @test */
    public function testThatGuestsCantAccessLanguagesAPI()
    {
        $this->get('/web/translation/languages')->assertStatus(401);
    }

    /** @test */
    public function testThatGuestsCantAccessLinesAPI()
    {
        $platform = factory(Platform::class)->create();

        $this->get('/web/translation/lines/'.$platform->id)->assertStatus(401);
    }

    public function testThatAdminsCanListLines()
    {
        $this->actingAs($this->makeAdminUser());

        $platform = factory(Platform::class)->create();

        $file = factory(File::class)->create([
            'platform_id' => $platform->id,
            'is_current' => true,
        ]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
        ]);

        $response = $this->get('/web/translation/lines/'.$platform->id);

        $response->assertSuccessful()->assertJsonStructure([
            [
                'serialized_line' => [
                    'value'
                ],
                'language' => [
                    'language'
                ],
                'key',
                'value'
            ],
        ]);
    }
}
