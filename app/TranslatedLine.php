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
        'needs_updating',
        'is_current',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'needs_updating' => 'boolean',
        'is_current' => 'boolean',
    ];

    /**
     * Get last modified by user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function language()
    {
        return $this->belongsTo(Language::class);
    }

    /**
     * Get the parent serialized line.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function serializedLine()
    {
        return $this->belongsTo(SerializedLine::class);
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopeCurrent($query) {
        return $query->where('is_current', true);
    }
}
