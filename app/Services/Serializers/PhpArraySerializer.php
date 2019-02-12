<?php

namespace App\Services\Serializers;

use App\File;
use App\Services\Contracts\SerializerInterface;

class PhpArraySerializer implements SerializerInterface
{
    public function serialize(File $file): array
    {
        throw new \Exception('PhpArraySerializer is under development');
    }
}
