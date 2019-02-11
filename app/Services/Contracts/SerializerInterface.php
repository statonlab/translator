<?php

namespace App\Services\Contracts;

use App\File;

interface SerializerInterface
{
    /**
     * Serialize the given file.
     *
     * The serialized data goes into the database.
     *
     * @param \App\File $file
     * @return array
     */
    public function serialize(File $file): array;
}
