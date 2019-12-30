<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatroomUser extends Model
{
    protected $table = 'chatroom_user';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}
