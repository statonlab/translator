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
}
