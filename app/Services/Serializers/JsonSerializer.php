<?php

namespace App\Services\Serializers;

use App\Exceptions\InvalidJsonKeyException;
use App\File;
use App\Services\Contracts\SerializerInterface;
use Illuminate\Support\Facades\Storage;

class JsonSerializer implements SerializerInterface
{
    /**
     * Serialize the file
     *
     * @param \App\File $file
     * @return array
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \App\Exceptions\InvalidJsonKeyException
     */
    public function serialize(File $file): array
    {
        $content = Storage::disk('files')->get($file->name);

        $data = json_decode($content, true);

        return $this->format($data);
    }

    /**
     * A wrapper for the recursive call.
     *
     * @param array $data
     * @return array
     * @throws \App\Exceptions\InvalidJsonKeyException
     */
    public function format(array $data): array
    {
        $items = [];

        $this->flatten($data, [], $items);

        return $items;
    }

    /**
     * Recursively go through all elements to flatten the array.
     *
     * @param array|string $line The current element.
     * @param array $previous_keys A list of the parent keys.
     * @param array $items The results array.
     * @throws \App\Exceptions\InvalidJsonKeyException
     */
    public function flatten($line, array $previous_keys, array &$items = [])
    {
        if (! is_array($line)) {
            $items[] = [
                'key' => implode('.', $previous_keys),
                'value' => $line,
            ];

            return;
        }

        foreach ($line as $key => $value) {
            if (strpos($key, '.') !== false) {
                throw new InvalidJsonKeyException("Keys must not contain the dot \".\" character");
            }

            $keys = array_merge($previous_keys, [$key]);
            $this->flatten($value, $keys, $items);
        }
    }
}
