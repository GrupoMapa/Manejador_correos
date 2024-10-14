<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FacMontosTributoFactura extends Model
{
    protected $table = 'fac_montos_tributo_facturas';

    protected $fillable = [
        'monto',
        'id_factura',
        'id_tipo_tributo',
    ];

    public function factura()
    {
        return $this->belongsTo(FacturaElectronica::class, 'id_factura');
    }

    public function tipoTributo()
    {
        return $this->belongsTo(FacTipoTributo::class, 'id_tipo_tributo');
    }
}