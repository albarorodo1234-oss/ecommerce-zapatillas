<?php

// app/Models/Marca.php
// TP10 (Parte B): MODELO Eloquent de la tabla "marcas" de solemate_db.
// Eloquent es el ORM de Laravel: cada fila de la tabla se convierte en un
// objeto Marca y las consultas se escriben con métodos de PHP (sin SQL a mano).
// Eloquent usa siempre consultas preparadas, así que también evita la
// inyección SQL.

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Marca extends Model
{
    // Tabla de solemate_db que representa este modelo.
    protected $table = 'marcas';

    // La tabla marcas no tiene las columnas created_at y updated_at.
    public $timestamps = false;

    // Relación "una marca tiene muchos productos" (productos.marca_id).
    public function productos(): HasMany
    {
        return $this->hasMany(Producto::class);
    }

    // Relación "a través de": los talles (stock) de todos los productos de la
    // marca. Une marcas → productos (marca_id) → producto_talle (producto_id).
    public function talles(): HasManyThrough
    {
        return $this->hasManyThrough(ProductoTalle::class, Producto::class);
    }

    // Resumen del reporte: cada marca con la cantidad de productos activos
    // (productos_count) y la suma de su stock (unidades), ordenadas por nombre.
    // withCount() y withSum() agregan esos totales en la misma consulta.
    public static function resumenDeStock(): Collection
    {
        return static::query()
            ->withCount(['productos' => fn ($consulta) => $consulta->where('activo', true)])
            ->withSum(['talles as unidades' => fn ($consulta) => $consulta->where('productos.activo', true)], 'stock')
            ->orderBy('nombre')
            ->get();
    }
}