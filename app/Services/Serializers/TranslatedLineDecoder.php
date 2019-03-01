<?php

namespace App\Services\Serializers;

use App\File;
use App\TranslatedLine;
use App\Language;

class TranslatedLineDecoder
{
    /**
     * Decode a language.
     *
     * @param \App\Language $language The language to obtain lines for.
     * @param \App\File $file If given, the lines for this file would be chosen instead of the
     *                  most current lines.
     *
     * @return array The decoded language array.
     */
    public function decode(Language $language, File $file = null): array
    {
        $lines = $language->translatedLines();
        if (! is_null($file)) {
            $lines->where('file_id', $file->id);
        } else {
            $lines->where('is_current', 1);
        }

        $lines = $lines->get();

        $decoded = [];
        foreach ($lines as $line) {
            $this->decodeLine($line, $decoded);
        }

        return $decoded;
    }

    /**
     * @param \App\TranslatedLine $line
     * @param array $decoded
     */
    public function decodeLine(TranslatedLine $line, array &$decoded)
    {
        $keys = array_reverse(explode('.', $line->key));
        $this->recursiveLineDecoder($line, $decoded, $keys);
    }

    /**
     * @param \App\TranslatedLine $line
     * @param array $decoded
     * @param array $keys
     */
    public function recursiveLineDecoder(TranslatedLine $line, array &$decoded, array $keys)
    {
        if (empty($keys)) {
            $decoded = $line->value;

            return;
        }

        // remove key
        $key = array_pop($keys);
        $decoded[$key] = [];
        $this->recursiveLineDecoder($line, $decoded[$key], $keys);
    }
}
