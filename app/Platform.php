<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Platform extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'name',
    ];

    /**
     * Get related languages.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function languages()
    {
        return $this->hasMany(Language::class);
    }

    /**
     * Get related files.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function files()
    {
        return $this->hasMany(File::class);
    }

    /**
     * Get assigned users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Gets translated lines.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function translatedLines()
    {
        return $this->hasManyThrough(TranslatedLine::class, Language::class);
    }

    /**
     * Get all serialized lines.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function serializedLines()
    {
        return $this->hasManyThrough(SerializedLine::class, File::class);
    }
}
