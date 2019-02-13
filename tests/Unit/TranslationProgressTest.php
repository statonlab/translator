<?php

namespace Tests\Unit;

use App\Language;
use App\Platform;
use App\Services\Translation\TranslationProgress;
use App\TranslatedLine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslationProgressTest extends TestCase
{
    use DatabaseTransactions;
    
    /** @test */
    public function testThatWeComputeProgressCorrectly()
    {
        $language = factory(Language::class)->create();

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $empty->fill(['value' => null])->save();

        // Should equal 50% translation
        $reporter = new TranslationProgress();
        $this->assertEquals(50, $reporter->compute($language));
    }

    /** @test */
    public function testThatPlatformProgressIsComputedCorrectly()
    {
        $platform = factory(Platform::class)->create();
        $language = factory(Language::class)->create([
            'platform_id' => $platform->id
        ]);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $empty->fill(['value' => null])->save();

        // Should equal 50% translation
        $reporter = new TranslationProgress();
        $this->assertEquals(50, $reporter->platform($platform));
    }

    /** @test */
    public function testThatUserProgressIsComputedCorrectly()
    {
        $user = $this->makeUser();
        $language = factory(Language::class)->create();

        $user->languages()->attach($language);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $empty->fill(['value' => null])->save();

        // Should equal 50% translation
        $reporter = new TranslationProgress();
        $this->assertEquals(50, $reporter->user($user));
    }

    /** @test */
    public function testThatUserProgressInAPlatformIsComputedCorrectly()
    {
        $platform = factory(Platform::class)->create();
        $user = $this->makeUser();
        $language = factory(Language::class)->create([
            'platform_id' => $platform->id
        ]);

        $user->languages()->attach($language);

        // 1 translated line
        $filled = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $filled->fill(['value' => 'with value'])->save();

        // And 1 empty line
        $empty = factory(TranslatedLine::class)->create([
            'language_id' => $language->id,
        ]);
        $empty->fill(['value' => null])->save();

        // Should equal 50% translation
        $reporter = new TranslationProgress();
        $this->assertEquals(50, $reporter->userPlatform($user, $platform));
    }
}
