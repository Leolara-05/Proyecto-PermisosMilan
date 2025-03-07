<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistorialPermiso extends Model
{
    use HasFactory;

    // Nombre de la tabla original
    protected $table = 'permisosmilan'; // Dejar como estaba originalmente

    protected $fillable = [
        'permiso_id',
        'usuario_id',
        'accion',
        'observaciones',
        'created_at',
        'updated_at'
    ];
}
