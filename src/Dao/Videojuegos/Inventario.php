<?php
namespace Dao\Videojuegos;

use Dao\Table;

class Inventario extends Table
{
    public static function getInventario($videojuegocod = 0)
    {
        $sql = "SELECT 
                    i.inventarioid, i.videojuegocod, i.stock, i.ubicacion,
                    v.titulo, v.precio, v.formato, v.imagen
                FROM inventario i
                INNER JOIN videojuegos v ON i.videojuegocod = v.videojuegocod";
        $params = [];

        if ($videojuegocod > 0) {
            $sql .= " WHERE i.videojuegocod = :videojuegocod";
            $params["videojuegocod"] = $videojuegocod;
        }
        $sql .= " ORDER BY i.inventarioid DESC";

        return self::obtenerRegistros($sql, $params);
    }

    public static function getInventarioById($inventarioid)
    {
        $sql = "SELECT 
                    i.inventarioid, i.videojuegocod, i.stock, i.ubicacion,
                    v.titulo, v.precio, v.formato, v.imagen
                FROM inventario i
                INNER JOIN videojuegos v ON i.videojuegocod = v.videojuegocod
                WHERE i.inventarioid = :inventarioid";
        $params = ["inventarioid" => $inventarioid];
        return self::obtenerUnRegistro($sql, $params);
    }

    public static function insertInventario($videojuegocod, $stock, $ubicacion)
    {
        $sql = "INSERT INTO inventario (videojuegocod, stock, ubicacion) 
                VALUES (:videojuegocod, :stock, :ubicacion)";
        $params = [
            "videojuegocod" => $videojuegocod,
            "stock" => $stock,
            "ubicacion" => $ubicacion
        ];
        return self::executeNonQuery($sql, $params);
    }

    public static function updateInventario($inventarioid, $videojuegocod, $stock, $ubicacion)
    {
        $sql = "UPDATE inventario 
                SET videojuegocod = :videojuegocod, stock = :stock, ubicacion = :ubicacion
                WHERE inventarioid = :inventarioid";
        $params = [
            "inventarioid" => $inventarioid,
            "videojuegocod" => $videojuegocod,
            "stock" => $stock,
            "ubicacion" => $ubicacion
        ];
        return self::executeNonQuery($sql, $params);
    }

    public static function deleteInventario($inventarioid)
    {
        $sql = "DELETE FROM inventario WHERE inventarioid = :inventarioid";
        $params = ["inventarioid" => $inventarioid];
        return self::executeNonQuery($sql, $params);
    }

    public static function getAllVideojuegos()
{
    $sql = "SELECT videojuegocod, titulo FROM videojuegos";
    return self::obtenerRegistros($sql, []);
}
}
