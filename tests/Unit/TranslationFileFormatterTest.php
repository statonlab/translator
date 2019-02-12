<?php

namespace Tests\Unit;

use App\File;
use App\Language;
use App\Services\Translation\TranslationFileFormatter;
use App\TranslatedLine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

class TranslationFileFormatterTest extends TestCase
{
    use DatabaseTransactions;

    public function testThatFileCreatedHasTheCorrectContent()
    {
        Storage::fake('files');

        /** @var File $file */
        $file = factory(File::class)->create([
            'is_current' => true,
        ]);

        /** @var Language $language */
        $language = factory(Language::class)->create([
            'platform_id' => $file->platform_id,
        ]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $language->id,
            'key' => 'key1.key2.key3',
            'value' => 'test',
        ]);

        $formatter = new TranslationFileFormatter($language);
        $content = Storage::disk('files')->get($formatter->create());
        $data = json_decode($content, true);
        $this->assertNotFalse($data);
        $this->assertIsArray($data);
        $this->assertArraySubset([
            'key1' => ['key2' => ['key3' => 'test']],
        ], $data);
    }
}
