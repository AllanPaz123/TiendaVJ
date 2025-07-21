<?php

namespace Controllers\Videojuegos;

use Controllers\PublicController;
use Dao\Videojuegos\Inventarios as InventarioDAO;
use Dao\Videojuegos\Videojuegos as VideojuegosDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Videojuegos-Inventario";
const XSR_KEY = "xsrToken_inventario";

class Inventario extends PublicController
{
    private array $viewData;
    private array $modes;

    public function __construct()
    {
        $this->modes = [
            "INS" => 'Agregando nuevo inventario',
            "UPD" => 'Editando inventario %s',
            "DEL" => 'Eliminando inventario %s',
            "DSP" => 'Detalle del inventario %s'
        ];

        $this->viewData = [
            "inventarioid" => 0,
            "videojuegocod" => "",
            "stock" => 0,
            "ubicacion" => "",
            "mode" => "",
            "modeDsc" => "",
            "errores" => [],
            "readonly" => "",
            "showAction" => true,
            "xsrToken" => "",
            "videojuegos" => [] // Para dropdown
        ];
    }

    public function run(): void
    {
        $this->capturarModoPantalla();
        $this->datosDeDao();
        if ($this->isPostBack()) {
            $this->datosFormulario();
            $this->validarDatos();
            if (count($this->viewData["errores"]) === 0) {
                $this->procesarDatos();
            }
        }
        $this->prepararVista();
        Renderer::render("Videojuegos/InventarioForm", $this->viewData);
    }

    private function throwError(string $message)
    {
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function capturarModoPantalla()
    {
        if (isset($_GET["mode"])) {
            $this->viewData["mode"] = $_GET["mode"];
            if (!isset($this->modes[$this->viewData["mode"]])) {
                $this->throwError("BAD REQUEST: Modo no válido.");
            }
        }
    }

    private function datosDeDao()
    {
        if ($this->viewData["mode"] !== "INS") {
            if (isset($_GET["inventarioid"])) {
                $this->viewData["inventarioid"] = intval($_GET["inventarioid"]);
                $inv = InventarioDAO::getInventarioById($this->viewData["inventarioid"]);
                if ($inv) {
                    $this->viewData = array_merge($this->viewData, $inv);
                } else {
                    $this->throwError("No se encontró el inventario solicitado.");
                }
            } else {
                $this->throwError("No se proporcionó el ID del inventario.");
            }
        }
        // Cargar videojuegos disponibles para combo
        $this->viewData["videojuegos"] = VideojuegosDAO::getVideojuegos();
    }

    private function datosFormulario()
    {
        foreach (["videojuegocod", "stock", "ubicacion", "xsrToken"] as $campo) {
            if (isset($_POST[$campo])) {
                $this->viewData[$campo] = $_POST[$campo];
            }
        }
        $this->viewData["stock"] = intval($this->viewData["stock"]);
        $this->viewData["videojuegocod"] = intval($this->viewData["videojuegocod"]);
    }

    private function validarDatos()
    {
        if ($this->viewData["videojuegocod"] <= 0) {
            $this->viewData["errores"]["videojuegocod"] = "Debe seleccionar un videojuego.";
        }
        if ($this->viewData["stock"] < 0) {
            $this->viewData["errores"]["stock"] = "El stock no puede ser negativo.";
        }
        if (Validators::IsEmpty($this->viewData["ubicacion"])) {
            $this->viewData["errores"]["ubicacion"] = "La ubicación es requerida.";
        }

        $tmpXsrToken = $_SESSION[XSR_KEY];
        if ($this->viewData["xsrToken"] !== $tmpXsrToken) {
            error_log("Token inválido en inventario.");
            $this->throwError("Solicitud inválida. Intente de nuevo.");
        }
    }

    private function procesarDatos()
    {
        switch ($this->viewData["mode"]) {
            case "INS":
                if (InventarioDAO::nuevoInventario(
                    $this->viewData["videojuegocod"],
                    $this->viewData["stock"],
                    $this->viewData["ubicacion"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Inventario agregado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al agregar el inventario."];
                }
                break;
            case "UPD":
                if (InventarioDAO::actualizarInventario(
                    $this->viewData["inventarioid"],
                    $this->viewData["videojuegocod"],
                    $this->viewData["stock"],
                    $this->viewData["ubicacion"]
                )) {
                    Site::redirectToWithMsg(LIST_URL, "Inventario actualizado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al actualizar el inventario."];
                }
                break;
            case "DEL":
                if (InventarioDAO::eliminarInventario($this->viewData["inventarioid"])) {
                    Site::redirectToWithMsg(LIST_URL, "Inventario eliminado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al eliminar el inventario."];
                }
                break;
        }
    }

    private function prepararVista()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["inventarioid"]
        );

        if (count($this->viewData["errores"]) > 0) {
            foreach ($this->viewData["errores"] as $campo => $error) {
                $this->viewData["error_" . $campo] = $error;
            }
        }

        if (in_array($this->viewData["mode"], ["DEL", "DSP"])) {
            $this->viewData["readonly"] = "readonly";
        }
        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["showAction"] = false;
        }

        $this->viewData["xsrToken"] = hash("sha256", random_int(0, 1000000) . time() . 'inventario' . $this->viewData["mode"]);
        $_SESSION[XSR_KEY] = $this->viewData["xsrToken"];
    }
}
