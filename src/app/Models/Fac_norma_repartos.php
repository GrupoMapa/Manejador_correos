<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fac_norma_repartos extends Model
{
    use HasFactory;
    //protected $connection = 'my_summay_almacenesbomba';

    protected $table = 'fac_norma_repartos';

    protected $fillable = [
        'nombre',
        'codigo',
    ];
}