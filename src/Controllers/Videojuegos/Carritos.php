<?php

namespace Controllers\Videojuegos;

use Controllers\PublicController;
use Dao\Videojuegos\Carrito;
use Views\Renderer;

class Carritos extends PublicController
{
    public function run(): void
    {
        $data["carritos"] = Carrito::getCarritos();
        Renderer::render("videojuegos/carritos", $data);
    }
}
