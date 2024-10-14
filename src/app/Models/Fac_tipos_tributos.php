<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fac_tipos_tributos extends Model
{
    use HasFactory;

    protected $table = 'fac_tipos_tributos';

    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'descripcion'
    ];
}