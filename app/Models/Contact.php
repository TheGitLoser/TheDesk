<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $table = 'contact_list';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }
}
