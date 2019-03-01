<?php

namespace Tests\Unit;

use App\File;
use App\Language;
use App\Services\Serializers\TranslatedLineDecoder;
use App\TranslatedLine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TranslatedLineDecoderTest extends TestCase
{
    use DatabaseTransactions;
    
    public function testThatDecodingResultsInTheAnticipatedArray()
    {
        $file = factory(File::class)->create();
        $language = factory(Language::class)->create();

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $language->id,
            'key' => 'key1.key1',
            'value' => 'value1',
        ]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $language->id,
            'key' => 'key1.key2',
            'value' => 'value2',
        ]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $language->id,
            'key' => 'key2',
            'value' => 'value',
        ]);

        factory(TranslatedLine::class)->create([
            'file_id' => $file->id,
            'language_id' => $language->id,
            'key' => 'key3.key2.key1',
            'value' => 'value',
        ]);

        $decoder = new TranslatedLineDecoder();
        $results = $decoder->decode($language, $file);

        $expected = [
            'key1' => [
                'key1' => 'value1',
                'key2' => 'value2',
            ],
            'key2' => 'value',
            'key3' => [
                'key2' => [
                    'key1' => 'value',
                ],
            ],
        ];

        // Full array check
        $this->assertEquals($expected, $results);

        // Key checks
        $this->assertArrayHasKey('key1', $results);
        $this->assertArrayHasKey('key2', $results['key1']);
        $this->assertArrayHasKey('key2', $results);
        $this->assertArrayHasKey('key3', $results);
        $this->assertArrayHasKey('key2', $results['key3']);
        $this->assertArrayHasKey('key1', $results['key3']['key2']);

        // Value checks
        $this->assertEquals('value', $results['key3']['key2']['key1']);
        $this->assertEquals('value1', $results['key1']['key1']);
        $this->assertEquals('value2', $results['key1']['key2']);
        $this->assertEquals('value', $results['key2']);
    }
}
