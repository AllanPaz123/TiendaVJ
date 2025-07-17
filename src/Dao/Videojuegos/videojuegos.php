<?php

namespace Dao\Videojuegos;

use Dao\Table;

class videojuegos extends Table
{
    public static function getVideojuegos()
    {
        $sqlstr = "SELECT * FROM videojuegos;";
        return self::obtenerRegistros($sqlstr, []);
    }

    public static function getVideojuegoById(int $videojuegocod)
    {
        $sqlstr = "SELECT * FROM videojuegos WHERE videojuegocod = :videojuegocod;";
        return self::obtenerUnRegistro($sqlstr, ["videojuegocod" => $videojuegocod]);
    }

    public static function nuevoVideojuego(
        string $titulo,
        string $descripcion,
        float $precio,
        string $imagen,
        string $archivo_descarga,
        string $formato,
        string $videojuegoest = 'ACT'
    ) {
        $sqlstr = "INSERT INTO videojuegos (titulo, descripcion, precio, imagen, archivo_descarga, formato, videojuegoest)
                   VALUES (:titulo, :descripcion, :precio, :imagen, :archivo_descarga, :formato, :videojuegoest);";
        return self::executeNonQuery(
            $sqlstr,
            [
                "titulo" => $titulo,
                "descripcion" => $descripcion,
                "precio" => $precio,
                "imagen" => $imagen,
                "archivo_descarga" => $archivo_descarga,
                "formato" => $formato,
                "videojuegoest" => $videojuegoest
            ]
        );
    }

    public static function actualizarVideojuego(
        int $videojuegocod,
        string $titulo,
        string $descripcion,
        float $precio,
        string $imagen,
        string $archivo_descarga,
        string $formato,
        string $videojuegoest
    ): int {
        $sqlstr = "UPDATE videojuegos 
                   SET titulo = :titulo, descripcion = :descripcion, precio = :precio,
                       imagen = :imagen, archivo_descarga = :archivo_descarga,
                       formato = :formato, videojuegoest = :videojuegoest
                   WHERE videojuegocod = :videojuegocod;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "titulo" => $titulo,
                "descripcion" => $descripcion,
                "precio" => $precio,
                "imagen" => $imagen,
                "archivo_descarga" => $archivo_descarga,
                "formato" => $formato,
                "videojuegoest" => $videojuegoest,
                "videojuegocod" => $videojuegocod
            ]
        );
    }

    public static function eliminarVideojuego(int $videojuegocod): int
    {
        $sqlstr = "DELETE FROM videojuegos WHERE videojuegocod = :videojuegocod;";
        return self::executeNonQuery(
            $sqlstr,
            [
                "videojuegocod" => $videojuegocod
            ]
        );
    }
}
