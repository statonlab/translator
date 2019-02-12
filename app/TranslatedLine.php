<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TranslatedLine extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'serialized_line_id',
        'language_id',
        'file_id',
        'key',
        'value',
        'user_id',
        'needs_updating',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'needs_updating' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeNeedsTranslation($query)
    {
        return $query->whereNull('value');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeNeedsUpdating($query)
    {
        return $query->where('needs_updating', true);
    }
}
