<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'HV_PERSONA'; // Nombre de la tabla en SQL Server

    protected $primaryKey = 'NUMERODOCUMENTO'; // La clave primaria es la cédula

    public $timestamps = false; // Si la tabla no tiene `created_at` y `updated_at`

    protected $fillable = [
        'NUMERODOCUMENTO',
        'PRIMERNOMBRE',
        'SEGUNDONOMBRE',
        'PRIMERAPELLIDO',
        'SEGUNDOAPELLIDO',
        'EMAIL'
    ];
}
