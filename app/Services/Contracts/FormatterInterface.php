<?php

namespace App\Services\Contracts;

use Illuminate\Support\Collection;

interface FormatterInterface
{
    public function make(Collection $lines): string;
}
