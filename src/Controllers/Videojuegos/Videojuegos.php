<?php

namespace Controllers\Videojuegos;

use Controllers\PrivateController;
use Dao\Videojuegos\Videojuegos as VideojuegosDAO;
use Views\Renderer;

class Videojuegos extends PrivateController
{
    private array $viewData;

    public function __construct()
    {
        parent::__construct();
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
