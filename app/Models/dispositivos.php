<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class dispositivos extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nombre'
    ];  
    public $timestamps = false;
    protected $table = 'dispositivos';
    public function cuarto()
    {
        return $this->belongsTo(cuartos::class, 'cuarto');
    }
    public function tipoDispositivo()
    {
        return $this->belongsTo(tipo_dispositivo::class,'tipo_dispositivo');
    }
}
