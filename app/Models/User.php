<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $primaryKey = 'user';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

}
