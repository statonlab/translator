<?php

namespace App\Services\Serializers;

use App\File;
use App\Services\Contracts\DecoderInterface;
use App\TranslatedLine;
use App\Language;

class TranslatedLineDecoder implements DecoderInterface
{
    /**
     * Decode a language from dot notation back into array format.
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
     * Decode a given line.
     *
     * @param \App\TranslatedLine $line
     * @param array $decoded
     */
    public function decodeLine(TranslatedLine $line, array &$decoded)
    {
        $keys = array_reverse(explode('.', $line->key));
        $this->recursiveLineDecoder($line, $decoded, $keys);
    }

    /**
     * Recursively iterate through lines and add keys to the decoded array.
     *
     * @param \App\TranslatedLine $line The line to translate.
     * @param array $decoded The decoded array.
     * @param array $keys The remaining keys.
     */
    public function recursiveLineDecoder(TranslatedLine $line, array &$decoded, array $keys)
    {
        if (empty($keys)) {
            $decoded = $line->value;

            return;
        }

        // remove key
        $key = array_pop($keys);

        if (! isset($decoded[$key])) {
            $decoded[$key] = [];
        }

        $this->recursiveLineDecoder($line, $decoded[$key], $keys);
    }
}
