<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MessageSeen extends Model
{
    protected $table = 'message_seen';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = NULL;
}