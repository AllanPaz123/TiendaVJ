<?php
namespace Controllers\Videojuegos;

use Dao\Videojuegos\Inventario as DaoInventario;
use Views\Renderer;

class inventariolist
{
    private $viewData = [];

    public function run(): void
    {
        $videojuegocod = intval($_GET["videojuegocod"] ?? 0);
        $this->viewData["filtro_videojuegocod"] = $videojuegocod;

        $this->viewData["videojuegos"] = DaoInventario::getAllVideojuegos();

        $this->viewData["inventario"] = DaoInventario::getInventario($videojuegocod);

        Renderer::render("Videojuegos/inventario_listado", $this->viewData);
    }
}
