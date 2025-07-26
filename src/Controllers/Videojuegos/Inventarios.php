<?php

namespace Controllers\Videojuegos;

use Controllers\PrivateController;
use Dao\Videojuegos\Inventarios as DaoInventario;
use Views\Renderer;

class Inventarios extends PrivateController
{
    private array $viewData;

    public function __construct()
    {
        parent::__construct();
        $this->viewData = [
            "inventario" => []
        ];
    }

    public function run(): void
    {
        $this->viewData["inventario"] = DaoInventario::obtenerTodos();
        Renderer::render("Videojuegos/inventarios", $this->viewData);
    }
}
