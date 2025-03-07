<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PermisoMilan extends Model
{
    use HasFactory;
    protected $table = 'permisosmilan';
    protected $fillable = [
        'cedula',
        'nombre',
        'cargo',
        'fecha_autorizacion',
        'desde',
        'hasta',
        'motivo_permiso',
        'descontable',
        'observaciones',
        'documento',
        'autorizado',
        'firma_trabajador',
    ];
}
