<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NewRequest extends Model
{
    protected $table = 'request';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}
