<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Get the default role.
     *
     * @return $this
     */
    public static function default()
    {
        return Role::where('name', 'User')->first();
    }
}
