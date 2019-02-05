<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'language',
        'image',
        'language_code',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
