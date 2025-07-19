<?php

namespace Controllers\Academic;

use Controllers\PublicController;
use views\Renderer; // aqui es donde agregue el alias para no comerme toda esa direccion
use Dao\Carros\Carros as carrosDAO; // aqui es donde agregue el aleas para no comerme toda esa direccion

class About extends PublicController
{
    public function run(): void
    {
        //iba a usar el codigo de abajo pero cree un aleas y lo llame solo Carros, para no poner esta direccion: \Dao\Carros\Carros
        //$carros = \Dao\Carros\Carros::obtenerCarros();
        $carros = carrosDAO::obtenerCarros();
        Renderer::render("academic/about", [
            "carros" => $carros,
        ]);
    }
}