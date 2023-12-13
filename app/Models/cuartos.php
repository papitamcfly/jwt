<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cuartos extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table = 'cuartos';
    public function propietario()
    {
        return $this->belongsTo(User::class, 'propietario');
    }
    public function dispositivos()
    {
        return $this->hasMany(dispositivos::class, 'cuarto');
    }
}
