<?php

// app/Models/Producto.php
// TP10 (Parte B): MODELO Eloquent de la tabla "productos" de solemate_db.
// Solo se usa para leer: el módulo de reportes no modifica la base.

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Producto extends Model
{
    protected $table = 'productos';

    // Convierte los tipos al leer: el precio llega como texto ("199900.00")
    // y activo como 0/1; así quedan como número y como true/false.
    protected function casts(): array
    {
        return [
            'precio' => 'float',
            'activo' => 'boolean',
        ];
    }

    // Relación "un producto pertenece a una marca" (productos.marca_id).
    public function marca(): BelongsTo
    {
        return $this->belongsTo(Marca::class);
    }

    // Relación "un producto tiene muchos talles con stock" (producto_talle.producto_id).
    public function talles(): HasMany
    {
        return $this->hasMany(ProductoTalle::class);
    }
}