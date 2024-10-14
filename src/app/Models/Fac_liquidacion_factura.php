<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fac_liquidacion_factura extends Model
{
    use HasFactory;
    //protected $connection = 'my_summay_almacenesbomba';

    protected $table = 'fac_liquidacion_facturas';

    protected $fillable = [
        'id_liquidacion',
        'id_factura',
        'created_at',
        'updated_at',
    ];

    // Define las relaciones con otras tablas
    public function liquidacion()
    {
        return $this->belongsTo(FacLiquidacion::class, 'id_liquidacion');
    }

    public function factura()
    {
        return $this->belongsTo(FacturaElectronica::class, 'id_factura');
    }
}
