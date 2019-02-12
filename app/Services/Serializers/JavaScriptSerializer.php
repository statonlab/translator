<?php

namespace App\Services\Serializers;

use App\File;
use App\Services\Contracts\SerializerInterface;

class JavaScriptSerializer implements SerializerInterface
{
    public function serialize(File $file): array
    {
        throw new \Exception('JavaScriptSerializer is under development');
    }
}
