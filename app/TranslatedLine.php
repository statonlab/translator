<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatedLine extends Model
{
    protected $fillable = [
        'serialized_line_id',
        'language_id',
        'value',
        'user_id'
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
