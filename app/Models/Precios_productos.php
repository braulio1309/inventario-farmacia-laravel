<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Precios_productos extends Model
{
    use HasFactory;

    protected $fillable = [
        'fraccion_id',
        'producto_id',
        'listaprecio_id',
        'precio',        
    ];
}


