<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fac_liquidacion extends Model
{
    use HasFactory;
    //protected $connection = 'my_summay_almacenesbomba';
    protected $table = 'fac_liquidacions';

    protected $fillable = [
        'created_at',
        'updated_at',
        'id_usuario',
        'id_area',
        'estado',
        'descripcion_general',
        'id_user_asignado',
        'fecha_asignacion',
        'id_user_tesoreria',
        'set_estado_2',
        'set_estado_3',
        'set_estado_5',
        'set_estado_6'
    ];

    public function area()
    {
        return $this->belongsTo(FacArea::class, 'id_area');
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function usuarioAsignado()
    {
        return $this->belongsTo(User::class, 'id_user_asignado');
    }

    public function usuarioTesoreria()
    {
        return $this->belongsTo(User::class, 'id_user_tesoreria');
    }
}
