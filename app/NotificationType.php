<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationType extends Model
{
    /** @var array  */
    protected $fillable = [
        'machine_name',
        'title',
        'is_private',
    ];

    /** @var array  */
    protected $casts = [
        'is_private' => 'boolean',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'notification_user');
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return mixed
     */
    public function scopePublic($query)
    {
        return $query->where('is_private', false)->whereNotNull('title');
    }
}
