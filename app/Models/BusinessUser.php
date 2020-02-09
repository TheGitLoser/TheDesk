<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessUser extends Model
{
    protected $table = 'business_user';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}
