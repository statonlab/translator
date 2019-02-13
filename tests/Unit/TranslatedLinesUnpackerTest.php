<?php

namespace Tests\Unit;

use App\Services\Serializers\TranslatedLinesUnpacker;
use App\TranslatedLine;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class TranslatedLinesUnpackerTest extends TestCase
{
    use DatabaseTransactions;

    public function testThatUnpackingWorks()
    {
        $lines = [];

        $lines[] = factory(TranslatedLine::class)->create([
            'key' => 'key1.key2.key3',
            'value' => 'test',
        ]);

        $lines[] = factory(TranslatedLine::class)->create([
            'key' => 'key1.key2.key4',
            'value' => 'test',
        ]);

        $lines[] = factory(TranslatedLine::class)->create([
            'key' => 'key1.key3',
            'value' => 'test',
        ]);

        $lines[] = factory(TranslatedLine::class)->create([
            'key' => 'key2',
            'value' => 'test',
        ]);

        $unpacker = new TranslatedLinesUnpacker();

        $data = $unpacker->unpack(collect($lines));

        $this->assertArraySubset([
            'key1' => [
                'key2' => [
                    'key3' => 'test',
                    'key4' => 'test',
                ],
                'key3' => 'test',
            ],
            'key2' => 'test',
        ], $data);
    }
}
