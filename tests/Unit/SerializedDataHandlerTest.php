<?php

namespace Tests\Unit;

use App\Language;
use App\Platform;
use App\Services\Translation\SerializedDataHandler;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\File;

class SerializedDataHandlerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Creates a fake file.
     *
     * @param $content
     * @return File
     */
    protected function makeFile($content, $platform_id)
    {
        Storage::fake('files');

        $file_name = uniqid();
        Storage::disk('files')->put($file_name, json_encode($content));

        return factory(File::class)->create([
            'name' => $file_name,
            'platform_id' => $platform_id,
        ]);
    }

    /** @test */
    public function testHandlerCreatesCorrectRecords()
    {
        /** @var Platform $platform */
        $platform = factory(Platform::class)->create();
        $data = [
            'key1' => [
                'key2' => [
                    'key3' => 'Line key1.key2.key3',
                ],
            ],
            'key2' => [
                'key3' => 'Line key2.key3',
            ],
        ];

        $file = $this->makeFile($data, $platform->id);
        $serialized = [
            [
                'key' => 'key1.key2.key3',
                'value' => 'Line key1.key2.key3',
            ],
            [
                'key' => 'key2.key3',
                'value' => 'Line key2.key3',
            ],
        ];

        // Create 2 different languages to test against.
        $languages = factory(Language::class, 2)->create([
            'platform_id' => $platform->id,
        ]);

        // Create the records
        $handler = new SerializedDataHandler($file, $serialized);
        $handler->createSerializedRecords();

        // We should have 2 serialized lines for this file.
        $this->assertEquals(2, $file->serializedLines->count());

        // All records are new to our languages so we should have 2 records
        // for each language related to this file.
        foreach ($languages as $language) {
            /** @var Language $language */
            $this->assertEquals(2,
                $language->translatedLines()->where('file_id', $file->id)->count());
            $this->assertEquals(2, $language->translatedLines()
                ->needsTranslation()
                ->where('file_id', $file->id)
                ->count());
        }

        // If we create a new file for the same platform with an edited line, we
        // should see 1 translated line that has needs_updating = true for each of
        // the languages.
        $serialized[1]['key'] = 'key2.key3';
        $serialized[1]['value'] = 'Updated line';
        $file = $this->makeFile($data, $platform->id);
        $handler = new SerializedDataHandler($file, $serialized);
        $handler->createSerializedRecords();

        foreach ($languages as $language) {
            $this->assertEquals(1, $language->translatedLines()
                ->needsUpdating()
                ->where('file_id', $file->id)
                ->count());
        }

        // Add translations for all languages to test the next issue
        foreach ($languages as $language) {
            $language->translatedLines->each(function ($line) {
                $line->fill(['value' => 'translation'])->save();
            });
        }

        // Adding a new entry should result in creating new record for each language
        $serialized[2]['key'] = 'new_key';
        $serialized[2]['value'] = 'New line';
        $file = $this->makeFile($data, $platform->id);
        $handler = new SerializedDataHandler($file, $serialized);
        $handler->createSerializedRecords();

        foreach ($languages as $language) {
            $this->assertEquals(1, $language->translatedLines()
                ->needsTranslation()
                ->where('file_id', $file->id)
                ->count(), 'There should be only 1 untranslated line for each language');
        }
    }
}
