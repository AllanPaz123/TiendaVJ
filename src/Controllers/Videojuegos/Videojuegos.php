<?php

namespace Controllers\Videojuegos;

use Controllers\PublicController;
use Dao\Videojuegos\Videojuegos as VideojuegosDAO;
use Views\Renderer;

class Videojuegos extends PublicController
{
    private array $viewData;

    public function __construct()
    {
        $this->viewData = [
            "videojuegos" => []
        ];
    }

    public function run(): void
    {
        $this->viewData["videojuegos"] = VideojuegosDAO::getVideojuegos();
        Renderer::render("Videojuegos/Videojuegos", $this->viewData);
    }
}
