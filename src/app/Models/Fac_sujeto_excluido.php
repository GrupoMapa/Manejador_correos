<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fac_sujeto_excluido extends Model
{
    use HasFactory;
    //protected $connection = 'my_summay_almacenesbomba';
    
    protected $table = 'fac_sujeto_excluido';
    protected $fillable = [
        'num_entrada_sap',
        'id_factura_electronica',
        'estado',
        'id_norma_reparto_sap',
        'id_user_asignado',
        'id_usuario',
        'fecha_asignacion',
        'set_estado_5',
        'set_estado_6'
    ];

    public function facturaElectronica()
    {
        return $this->belongsTo(FacturaElectronica::class, 'id_factura_electronica');
    }

    public function normaRepartoSap()
    {
        return $this->belongsTo(NormaRepartoSap::class, 'id_norma_reparto_sap');
    }

    public function userAsignado()
    {
        return $this->belongsTo(User::class, 'id_user_asignado');
    }
  
}
