<?php

namespace Controllers\Videojuegos;

use Controllers\PrivateController;
use Dao\Videojuegos\Carrito;
use Views\Renderer;

class Carritos extends PrivateController
{
    public function run(): void
    {
        parent::__construct();
        $data["carritos"] = Carrito::getCarritos();
        Renderer::render("videojuegos/carritos", $data);
    }
}
