<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    // default table name => {class name}='s'
    protected $table = 'my_flights';
    // default PK = id
    protected $primaryKey = 'flight_id';
    // default auto increase PK
    public $incrementing = false;
    // default primary key type = int
    protected $keyType = 'string';
    // default auto update 'created_at' & 'updated_at' 
    public $timestamps = false;
    // default 'created_at' & 'updated_at'
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';

}
