<?php

// app/Models/ProductoTalle.php
// TP10 (Parte B): MODELO Eloquent de la tabla "producto_talle" de solemate_db
// (stock de cada producto por talle).

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductoTalle extends Model
{
    // Por convención Laravel buscaría la tabla "producto_talles" (en plural);
    // en solemate_db se llama "producto_talle", por eso se indica a mano.
    protected $table = 'producto_talle';

    // La tabla producto_talle no tiene created_at ni updated_at.
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'talle' => 'integer',
            'stock' => 'integer',
        ];
    }

    // Relación "un talle pertenece a un producto".
    public function producto(): BelongsTo
    {
        return $this->belongsTo(Producto::class);
    }
}