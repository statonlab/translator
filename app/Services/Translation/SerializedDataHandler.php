<?php

namespace App\Services\Translation;

use App\File;
use App\Language;
use App\SerializedLine;
use App\TranslatedLine;

class SerializedDataHandler
{
    /**
     * @var \Illuminate\Support\Collection
     */
    protected $serialized;

    /**
     * @var File
     */
    protected $file;

    /**
     * The file this current one is going to replace.
     *
     * @var File|Null
     */
    protected $previous_file;

    /**
     * SerializedDataHandler constructor.
     *
     * @param File $file
     * @param array $serialized
     */
    public function __construct(File $file, array $serialized)
    {
        $this->serialized = collect($serialized);
        $this->file = $file;

        $this->previous_file = File::where('id', '!=', $file->id)
            ->orderBy('id', 'desc')
            ->first();
    }

    /**
     * Creates the serialized records.
     */
    public function createSerializedRecords()
    {
        $records = $this->serialized->map(function (array $record) {
            return [
                'file_id' => $this->file->id,
                'key' => trim($record['key']),
                'value' => trim($record['value']),
            ];
        });

        // Chunk them to 100 lines at a time so the DB doesn't choke.
        foreach ($records->chunk(100) as $chunk) {
            /** @var \Illuminate\Support\Collection $chunk */
            SerializedLine::insert($chunk->toArray());
        }

        // Now create the related records.
        $this->createTranslationLines();
    }

    /**
     * Create the translation lines.
     */
    protected function createTranslationLines()
    {
        // For every language the current platform is associated with.
        $this->file->platform->languages->each(function ($language) {
            $this->createTranslatedLinesForLanguage($language);
        });
    }

    /**
     * Copy previously translated lines into the new set of lines or create
     * a new empty line to notify the user of the availability of new lines.
     *
     * @param \App\Language $language
     */
    protected function createTranslatedLinesForLanguage(Language $language)
    {
        // For every serialized line that's related to the current file.
        $this->file->serializedLines->each(function ($line) use ($language) {
            if ($this->previous_file) {
                // Find the equivalent line form the previous file for the same language.
                $pre_translated_line = TranslatedLine::where([
                    'file_id' => $this->previous_file->id,
                    'language_id' => $language->id,
                    'key' => $line->key,
                ])->first();

                if ($pre_translated_line) {
                    $this->copyPreviousLine($line, $pre_translated_line);

                    return;
                }
            }

            // A previous file doesn't exist or a previously translated line
            // doesn't exist. Therefore, create a new line for this language.
            $this->createNewLine($line, $language);
        });
    }

    /**
     * Copy a previous line into the new set of lines.
     *
     * @param \App\SerializedLine $line
     * @param \App\TranslatedLine $previous_line
     * @return \App\TranslatedLine
     */
    protected function copyPreviousLine(SerializedLine $line, TranslatedLine $previous_line): TranslatedLine
    {
        return TranslatedLine::create([
            'file_id' => $this->file->id,
            'language_id' => $previous_line->language_id,
            'user_id' => $previous_line->user_id,
            'serialized_line_id' => $line->id,
            'key' => $line->key,
            'value' => $previous_line->value,
            'needs_updating' => $this->needsUpdating($line),
        ]);
    }

    /**
     * Create a new empty line.
     *
     * @param \App\SerializedLine $line
     * @param \App\Language $language
     * @return \App\TranslatedLine
     */
    protected function createNewLine(SerializedLine $line, Language $language): TranslatedLine
    {
        return TranslatedLine::create([
            'file_id' => $this->file->id,
            'language_id' => $language->id,
            'serialized_line_id' => $line->id,
            'key' => $line->key,
        ]);
    }

    /**
     * Checks whether a line needs updating.
     *
     * @param \App\SerializedLine $new_line
     * @return bool
     */
    protected function needsUpdating(SerializedLine $new_line): bool
    {
        /** @var SerializedLine $previous_line */
        $previous_line = $this->previous_file->serializedLines()
            ->where('key', $new_line->key)
            ->first();

        if ($previous_line) {
            return $previous_line->value !== $new_line->value;
        }

        return false;
    }
}
