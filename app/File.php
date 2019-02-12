<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    /**
     * Fillable columns.
     *
     * @var array
     */
    protected $fillable = [
        'platform_id',
        'name',
        'app_version',
        'path',
    ];

    /**
     * The platform this file belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function platform()
    {
        return $this->belongsTo(Platform::class);
    }

    /**
     * Get all serialized lines.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function serializedLines()
    {
        return $this->hasMany(SerializedLine::class);
    }

    /**
     * Get all translated lines.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function translatedLines()
    {
        return $this->hasMany(TranslatedLine::class);
    }
}
