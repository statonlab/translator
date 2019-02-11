<?php

namespace App\Helpers;

class FileHelper
{
    /**
     * The path to the file.
     *
     * @var string
     */
    protected $path;

    /**
     * FileHelpers constructor.
     *
     * @param string $path
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * The name of the file.
     *
     * @return string
     */
    public function name(): string
    {
        return basename($this->path);
    }

    /**
     * The extension.
     *
     * @return string
     */
    public function extension(): string
    {
        $info = pathinfo($this->path);

        return $info['extension'] ?? null;
    }
}
