<?php

namespace Controllers\Videojuegos;

use Controllers\PrivateController;
use Views\Renderer;
use Dao\Videojuegos\Inventarios as DaoInventario;
use Utilities\Site;

class Inventario extends PrivateController
{
    private array $viewData = [];
    private string $mode = "DSP";
    private array $modeDescriptions = [
        "DSP" => "Detalle de inventario %s",
        "INS" => "Nuevo Registro de Inventario",
        "UPD" => "Editar inventario %s",
        "DEL" => "Eliminar inventario %s"
    ];

    public function run(): void
    {
        $this->viewData = [
            "mode" => "DSP",
            "inventarioid" => "",
            "videojuegocod" => "",
            "stock" => "0",
            "ubicacion" => "",
            "modeDsc" => "",
            "readonly" => "",
            "showAction" => true,
            "xsrToken" => ""
        ];

        $this->mode = $_GET["mode"] ?? "DSP";
        $id = $_GET["inventarioid"] ?? null;

        if (!in_array($this->mode, ["DSP", "INS", "UPD", "DEL"])) {
            Site::redirectToWithMsg("index.php?page=Videojuegos-Inventarios", "Modo no válido");
        }

        if ($this->mode !== "INS" && !$id) {
            Site::redirectToWithMsg("index.php?page=Videojuegos-Inventarios", "ID requerido");
        }

        if ($id) {
            $tmp = DaoInventario::obtenerPorId(intval($id));
            if (!$tmp) {
                Site::redirectToWithMsg("index.php?page=Videojuegos-Inventarios", "Registro no encontrado");
            }
            $this->viewData = array_merge($this->viewData, $tmp);
        }

        if ($this->isPostBack()) {
            $this->viewData["videojuegocod"] = $_POST["videojuegocod"] ?? "";
            $this->viewData["stock"] = $_POST["stock"] ?? "0";
            $this->viewData["ubicacion"] = $_POST["ubicacion"] ?? "";
            $this->viewData["xsrToken"] = $_POST["xsrToken"] ?? "";

            if ($this->viewData["xsrToken"] !== $_SESSION["xsrTokenInventario"]) {
                Site::redirectToWithMsg("index.php?page=Videojuegos-Inventarios", "Token inválido");
            }

            $hasErrors = false;

            if (!filter_var($this->viewData["videojuegocod"], FILTER_VALIDATE_INT)) {
                $this->viewData["error_videojuegocod"] = "Código inválido";
                $hasErrors = true;
            }

            if (!filter_var($this->viewData["stock"], FILTER_VALIDATE_INT)) {
                $this->viewData["error_stock"] = "Stock inválido";
                $hasErrors = true;
            }

            if (empty($this->viewData["ubicacion"])) {
                $this->viewData["error_ubicacion"] = "Debe indicar una ubicación";
                $hasErrors = true;
            }

            if (!$hasErrors) {
                $datosGuardar = [
                    "videojuegocod" => intval($this->viewData["videojuegocod"]),
                    "stock" => intval($this->viewData["stock"]),
                    "ubicacion" => $this->viewData["ubicacion"]
                ];
                if ($this->mode !== "INS") {
                    $datosGuardar["inventarioid"] = intval($this->viewData["inventarioid"]);
                }

                switch ($this->mode) {
                    case "INS":
                        DaoInventario::insertar($datosGuardar);
                        break;
                    case "UPD":
                        DaoInventario::actualizar($datosGuardar);
                        break;
                    case "DEL":
                        DaoInventario::eliminar(intval($this->viewData["inventarioid"]));
                        break;
                }
                Site::redirectToWithMsg("index.php?page=Videojuegos-Inventarios", "Operación ejecutada correctamente");
            }
        }

        $this->viewData["mode"] = $this->mode;
        $this->viewData["modeDsc"] = sprintf($this->modeDescriptions[$this->mode], $this->viewData["inventarioid"]);
        $this->viewData["readonly"] = ($this->mode === "DSP" || $this->mode === "DEL") ? "readonly" : "";
        $this->viewData["showAction"] = !($this->mode === "DSP");
        $this->viewData["xsrToken"] = hash("sha256", random_int(0, 1000000) . time() . 'inventario' . $this->mode);
        $_SESSION["xsrTokenInventario"] = $this->viewData["xsrToken"];

        Renderer::render("Videojuegos/inventario", $this->viewData);
    }
}
