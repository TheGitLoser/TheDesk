<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $table = 'message';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}
