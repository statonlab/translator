<?php

namespace Tests\Unit;

use App\Exceptions\InvalidJsonKeyException;
use App\File;
use App\Services\Serializers\JsonSerializer;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class JsonSerializerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Creates a fake file.
     *
     * @param $content
     * @return File
     */
    protected function makeFile($content)
    {
        Storage::fake('files');

        $file_name = uniqid();
        Storage::disk('files')->put($file_name, json_encode($content));

        return factory(File::class)->create([
            'name' => $file_name,
        ]);
    }

    /** @test */
    public function testSerializerGetsCorrectOutput()
    {
        $serializer = new JsonSerializer();

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

        $should_become = [
            [
                'key' => 'key1.key2.key3',
                'value' => 'Line key1.key2.key3',
            ],
            [
                'key' => 'key2.key3',
                'value' => 'Line key2.key3',
            ],
        ];
        $file = $this->makeFile($data);
        $results = $serializer->serialize($file);

        $this->assertArraySubset($should_become, $results);
    }

    /** @test */
    public function testThatKeysThatHaveDotsInThemThrowAnException()
    {
        $file = $this->makeFile([
            'key1.key2' => 'invalid key',
        ]);
        $serializer = new JsonSerializer();

        $this->expectException(InvalidJsonKeyException::class);
        $serializer->serialize($file);
    }
}
