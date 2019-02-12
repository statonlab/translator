<?php

namespace App\Services\Formatters;

use App\Services\Contracts\FormatterInterface;
use App\Services\Serializers\TranslatedLinesUnpacker;
use App\TranslatedLine;
use Illuminate\Support\Collection;

class JsonFormatter implements FormatterInterface
{
    /**
     * @param array $lines
     * @return string
     */
    public function make(Collection $lines): string
    {
        $upacked = (new TranslatedLinesUnpacker())->unpack($lines);

        return json_encode($upacked, JSON_PRETTY_PRINT);
    }
}
