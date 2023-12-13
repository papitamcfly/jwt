<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipo_dispositivo extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $table = 'tipo_dispositivos';
    public function dispositivos()
    {
        return $this->hasMany(dispositivos::class, 'tipo_dispositivo');
    }
}
