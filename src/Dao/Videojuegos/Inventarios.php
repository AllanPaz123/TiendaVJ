<?php

namespace Dao\Videojuegos;

use Dao\Table;

class Inventarios extends Table
{
    public static function obtenerTodos()
    {
        return self::obtenerRegistros("SELECT * FROM inventario", []);
    }

    public static function obtenerPorId(int $id)
    {
        return self::obtenerUnRegistro("SELECT * FROM inventario WHERE inventarioid = :id", ["id" => $id]);
    }

    public static function insertar(array $data)
    {
        $sql = "INSERT INTO inventario (videojuegocod, stock, ubicacion) VALUES (:videojuegocod, :stock, :ubicacion)";
        return self::executeNonQuery($sql, $data);
    }

    public static function actualizar(array $data)
    {
        $sql = "UPDATE inventario SET videojuegocod = :videojuegocod, stock = :stock, ubicacion = :ubicacion WHERE inventarioid = :inventarioid";
        return self::executeNonQuery($sql, $data);
    }

    public static function eliminar(int $id)
    {
        $sql = "DELETE FROM inventario WHERE inventarioid = :id";
        return self::executeNonQuery($sql, ["id" => $id]);
    }
}
