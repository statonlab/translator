<?php

namespace App\Exceptions;

use Exception;

class InvalidJsonKeyException extends Exception
{
    private $parent_keys = [];

    private $key = '';

    public function parentKeys($keys)
    {
        $this->parent_keys = $keys;

        return $this;
    }

    public function key($key)
    {
        $this->key = $key;

        return $this;
    }

    public function getParentKeys()
    {
        return $this->parent_keys;
    }

    public function getKey()
    {
        return $this->key;
    }
}
