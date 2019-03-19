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
     * @var File
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
            ->where('platform_id', $file->platform_id)
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
                'created_at' => now(),
                'updated_at' => now(),
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
    protected function createTranslationLines(): void
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
    protected function createTranslatedLinesForLanguage(Language $language): void
    {
        // For every serialized line that's related to the current file.
        $this->file->serializedLines->each(function ($line) use ($language) {
            // Find the equivalent line form the previous file for the same language.
            $pre_translated_line = TranslatedLine::where([
                'language_id' => $language->id,
                'key' => $line->key,
                'is_current' => true,
            ])->first();

            if ($pre_translated_line) {
                $pre_translated_line->fill(['is_current' => false])->save();
                $this->copyPreviousLine($line, $pre_translated_line);

                return;
            }

            // A previous file doesn't exist or a previously translated line
            // doesn't exist. Therefore, create a new line for this language.
            $this->createNewLine($line, $language);
        });

        // Finally, clear lines that have is_current = true but do not belong to this file
        TranslatedLine::where('file_id', '!=', $this->file->id)->where([
            'is_current' => true,
            'language_id' => $language->id,
        ])->update([
            'is_current' => false,
        ]);
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
            'serialized_line_id' => $line->id,
            'key' => $line->key,
            'value' => $previous_line->value,
            'needs_updating' => $previous_line->needs_updating || $this->needsUpdating($line),
            'is_current' => true,
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
            'is_current' => true,
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
        if ($this->previous_file) {
            $previous_line = SerializedLine::where('file_id', $this->previous_file->id)
                ->where('key', $new_line->key)
                ->first();

            if ($previous_line) {
                return $previous_line->value !== $new_line->value;
            }
        }

        return false;
    }
}
