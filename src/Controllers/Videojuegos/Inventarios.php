<?php

namespace Controllers\Videojuegos;

use Controllers\PublicController;
use Dao\Videojuegos\Inventarios as InventarioDAO;
use Views\Renderer;

class Inventario extends PublicController
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
        $this->viewData["inventario"] = InventarioDAO::getInventario();
        Renderer::render("Videojuegos/Inventario", $this->viewData);
    }
}
