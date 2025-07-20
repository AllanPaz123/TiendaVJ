<?php

namespace Dao\Videojuegos;

use Dao\Table;

class Carrito extends Table
{
    public static function getCarritos()
    {
        $sqlstr = "SELECT * FROM carrito;";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getCarritoById(int $carritoid)
    {
        $sqlstr = "SELECT * FROM carrito WHERE carritoid = :carritoid;";
        return self::obtenerUnRegistro($sqlstr, ["carritoid" => $carritoid]);
    }

    public static function nuevoCarrito(
        int $usercod,
        int $videojuegocod,
        int $cantidad,
        string $tipo_entrega
    ) {
        $sqlstr = "INSERT INTO carrito (usercod, videojuegocod, cantidad, tipo_entrega) 
                   VALUES (:usercod, :videojuegocod, :cantidad, :tipo_entrega);";
        return self::executeNonQuery(
            $sqlstr,
            [
                "usercod" => $usercod,
                "videojuegocod" => $videojuegocod,
                "cantidad" => $cantidad,
                "tipo_entrega" => $tipo_entrega
            ]
        );
    }

    public static function actualizarCarrito(
        int $carritoid,
        int $usercod,
        int $videojuegocod,
        int $cantidad,
        string $tipo_entrega
    ): int {
        $sqlstr = "UPDATE carrito 
                   SET usercod = :usercod, 
                       videojuegocod = :videojuegocod, 
                       cantidad = :cantidad, 
                       tipo_entrega = :tipo_entrega 
                   WHERE carritoid = :carritoid;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "usercod" => $usercod,
                "videojuegocod" => $videojuegocod,
                "cantidad" => $cantidad,
                "tipo_entrega" => $tipo_entrega,
                "carritoid" => $carritoid
            ]
        );
    }

    public static function eliminarCarrito(int $carritoid): int
    {
        $sqlstr = "DELETE FROM carrito WHERE carritoid = :carritoid;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "carritoid" => $carritoid
            ]
        );
    }
}
