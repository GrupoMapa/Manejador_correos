<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestGeneral extends Model
{
    use HasFactory;
    protected $table = 'test_general';

    protected $fillable = [
        'texto',
        'estado',
    ];
}
