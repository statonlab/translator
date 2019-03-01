<?php

namespace App\Services\Contracts;

use App\File;
use App\Language;

interface DecoderInterface
{
    /**
     * Decodes a file back into array format.
     *
     * @param \App\Language $language
     * @param \App\File|null $file
     * @return array
     */
    public function decode(Language $language, File $file = null): array;
}
