<?php

namespace App\Services\Translation;

use App\Language;
use App\Services\Formatters\JsonFormatter;
use Illuminate\Support\Facades\Storage;

class TranslationFileFormatter
{
    /**
     * @var \App\Language
     */
    protected $language;

    /**
     * @var \App\Services\Contracts\FormatterInterface
     */
    protected $formatter;

    /**
     * TranslationFileFormatter constructor.
     *
     * @param \App\Language $language
     */
    public function __construct(Language $language)
    {
        $this->language = $language;
        $this->formatter = new JsonFormatter();
    }

    /**
     * Create a file with contents of the given language.
     *
     * @return string
     */
    public function create(): string {
        /** @var \App\File $file */
        $file = $this->language->platform->files()->current()->first();

        if(!$file) {
            return null;
        }

        $name = uniqid().'.json';
        $path = null;
        $lines = $file->translatedLines()->orderBy('id', 'asc')->get();
        $contents = $this->formatter->make($lines);

        Storage::disk('files')->put($name, $contents);

        return $name;
    }
}
