<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SerializedLine extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'file_id',
        'key',
        'value',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
