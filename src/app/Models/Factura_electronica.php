<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Factura_electronica extends Model
{
    use HasFactory;
    //protected $connection = 'my_summay_almacenesbomba';
    
    protected $table = 'factura_electronicas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'codigoGeneracion',
        'nombreComercial',
        'nit',
        'telefono',
        'totalPagar',
        'pdf',
        'json',
        'fechaEmision',
        'direccion_origen',
        'fecha_correo',
        'json_tributos',
        'sello',
        'tipo_dte',
        'message_id',
        'local_sello',
        'local_codigo',
        'local_asiento_diario',
        'set_entrada_merc',

        'iva_percibido',
        'valor_operaciones',
        'monto_sujeto_percepcion',
        'numero_control'

    ];

    /**
     * Liquidacions
     */
    public function liquidaciones(){
        return $this->belongsToMany(Fac_liquidacion::class, 'fac_liquidacion_facturas', 'id_factura', 'id_liquidacion');
    }
}
