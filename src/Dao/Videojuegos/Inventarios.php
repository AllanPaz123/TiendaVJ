<?php

namespace Dao\Videojuegos;

use Dao\Table;

class inventarios extends Table
{
    public static function getInventario()
    {
        $sqlstr = "SELECT * FROM inventario;";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getInventarioById(int $inventarioid)
    {
        $sqlstr = "SELECT * FROM inventario WHERE inventarioid = :inventarioid;";
        return self::obtenerUnRegistro($sqlstr, ["inventarioid" => $inventarioid]);
    }

    public static function nuevoInventario(
        int $videojuegocod,
        int $stock,
        string $ubicacion
    ) {
        $sqlstr = "INSERT INTO inventario (videojuegocod, stock, ubicacion)
                   VALUES (:videojuegocod, :stock, :ubicacion);";
        return self::executeNonQuery(
            $sqlstr,
            [
                "videojuegocod" => $videojuegocod,
                "stock" => $stock,
                "ubicacion" => $ubicacion
            ]
        );
    }

    public static function actualizarInventario(
        int $inventarioid,
        int $videojuegocod,
        int $stock,
        string $ubicacion
    ): int {
        $sqlstr = "UPDATE inventario 
                   SET videojuegocod = :videojuegocod, stock = :stock, ubicacion = :ubicacion
                   WHERE inventarioid = :inventarioid;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "videojuegocod" => $videojuegocod,
                "stock" => $stock,
                "ubicacion" => $ubicacion,
                "inventarioid" => $inventarioid
            ]
        );
    }

    public static function eliminarInventario(int $inventarioid): int
    {
        $sqlstr = "DELETE FROM inventario WHERE inventarioid = :inventarioid;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "inventarioid" => $inventarioid
            ]
        );
    }
}
