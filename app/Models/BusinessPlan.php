<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BusinessPlan extends Model
{
    protected $table = 'business_plan';
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}
