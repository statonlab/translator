<?php

namespace App\Services\Serializers;

use App\TranslatedLine;
use Illuminate\Support\Collection;

class TranslatedLinesUnpacker
{
    /**
     * Recreates the original array.
     *
     * @param TranslatedLine[] $lines An array of translated lines.
     * @return array
     */
    public function unpack(Collection $lines): array
    {
        $data = [];

        foreach ($lines as $line) {
            /** @var TranslatedLine $line */
            $keys = array_reverse(explode('.', $line->key));
            $this->buildNestedKeys($keys, $data, $line->value);
        }

        return $data;
    }

    /**
     * Recursively build the list of nested keys.
     *
     * @param array $keys Keys of the array.
     * @param array $data The data to add the keys to.
     * @param string $line The line to build.
     */
    protected function buildNestedKeys(array &$keys, array &$data, string $line)
    {
        if (count($keys) === 1) {
            $key = array_pop($keys);
            $data[$key] = $line;

            return;
        }

        $key = array_pop($keys);
        if (! isset($data[$key])) {
            $data[$key] = [];
        }

        $this->buildNestedKeys($keys, $data[$key], $line);
    }
}
