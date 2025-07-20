<?php

namespace Controllers\Videojuegos;

use Dao\Videojuegos\Carrito as CarritoDAO;
use Controllers\PublicController;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Videojuegos-Carritos";
const XSR_KEY = "xsrToken_carrito";

class Carrito extends PublicController
{
    private array $viewData;
    private array $entregas;
    private array $modes;

    public function __construct()
    {
        $this->modes = [
            "INS" => 'Agregando nuevo Carrito',
            "UPD" => 'Modificando Carrito %s',
            "DEL" => 'Eliminando Carrito %s',
            "DSP" => 'Mostrando Detalle de Carrito %s'
        ];
        $this->entregas = ["digital", "fisico"];
        $this->viewData = [
            "carritoid" => 0,
            "usercod" => 0,
            "videojuegocod" => 0,
            "cantidad" => 1,
            "tipo_entrega" => "digital",
            "agregado_en" => "",
            "mode" => "",
            "modeDsc" => "",
            "entrega_digital" => "",
            "entrega_fisico" => "",
            "errores" => [],
            "readonly" => "",
            "showAction" => true,
            "xsrToken" => ""
        ];
    }

    public function run(): void
    {
        $this->capturarModo();
        $this->cargarDatosDAO();
        if ($this->isPostBack()) {
            $this->cargarDatosFormulario();
            $this->validarDatos();
            if (count($this->viewData["errores"]) === 0) {
                $this->procesarAccion();
            }
        }
        $this->prepararVista();
        Renderer::render("videojuegos/carrito", $this->viewData);
    }

    private function throwError(string $message)
    {
        Site::redirectToWithMsg(LIST_URL, $message);
    }

    private function capturarModo()
    {
        if (!isset($_GET["mode"]) || !isset($this->modes[$_GET["mode"]])) {
            $this->throwError("Modo no definido o inválido");
        }
        $this->viewData["mode"] = $_GET["mode"];
    }

    private function cargarDatosDAO()
    {
        if ($this->viewData["mode"] !== "INS") {
            if (!isset($_GET["id"])) {
                $this->throwError("Identificador de Carrito requerido");
            }
            $this->viewData["carritoid"] = intval($_GET["id"]);
            $carrito = CarritoDAO::getCarritoById($this->viewData["carritoid"]);
            if (!$carrito) {
                $this->throwError("Carrito no encontrado");
            }
            foreach ($carrito as $key => $value) {
                $this->viewData[$key] = $value;
            }
        }
    }

    private function cargarDatosFormulario()
    {
        $campos = ["usercod", "videojuegocod", "cantidad", "tipo_entrega", "xsrToken"];
        foreach ($campos as $campo) {
            if (isset($_POST[$campo])) {
                $this->viewData[$campo] = $_POST[$campo];
            }
        }
    }

    private function validarDatos()
    {
        if (!is_numeric($this->viewData["usercod"]) || $this->viewData["usercod"] <= 0) {
            $this->viewData["errores"]["usercod"] = "Código de usuario inválido";
        }
        if (!is_numeric($this->viewData["videojuegocod"]) || $this->viewData["videojuegocod"] <= 0) {
            $this->viewData["errores"]["videojuegocod"] = "Código de videojuego inválido";
        }
        if (!is_numeric($this->viewData["cantidad"]) || $this->viewData["cantidad"] < 1) {
            $this->viewData["errores"]["cantidad"] = "Cantidad inválida";
        }
        if (!in_array($this->viewData["tipo_entrega"], $this->entregas)) {
            $this->viewData["errores"]["tipo_entrega"] = "Tipo de entrega no válido";
        }

        if ($this->viewData["xsrToken"] !== ($_SESSION[XSR_KEY] ?? "")) {
            $this->throwError("Token inválido. Intente nuevamente.");
        }
    }

    private function procesarAccion()
    {
        switch ($this->viewData["mode"]) {
            case "INS":
                $res = CarritoDAO::nuevoCarrito(
                    $this->viewData["usercod"],
                    $this->viewData["videojuegocod"],
                    $this->viewData["cantidad"],
                    $this->viewData["tipo_entrega"]
                );
                if ($res) {
                    Site::redirectToWithMsg(LIST_URL, "Carrito agregado exitosamente.");
                }
                break;
            case "UPD":
                $res = CarritoDAO::actualizarCarrito(
                    $this->viewData["carritoid"],
                    $this->viewData["usercod"],
                    $this->viewData["videojuegocod"],
                    $this->viewData["cantidad"],
                    $this->viewData["tipo_entrega"]
                );
                if ($res) {
                    Site::redirectToWithMsg(LIST_URL, "Carrito actualizado exitosamente.");
                }
                break;
            case "DEL":
                $res = CarritoDAO::eliminarCarrito($this->viewData["carritoid"]);
                if ($res) {
                    Site::redirectToWithMsg(LIST_URL, "Carrito eliminado exitosamente.");
                }
                break;
        }
        $this->viewData["errores"]["global"] = ["Ocurrió un error al procesar la solicitud."];
    }

    private function prepararVista()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["carritoid"]
        );

        $this->viewData["entrega_" . $this->viewData["tipo_entrega"]] = "selected";

        if (in_array($this->viewData["mode"], ["DEL", "DSP"])) {
            $this->viewData["readonly"] = "readonly";
        }

        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["showAction"] = false;
        }

        $this->viewData["xsrToken"] = hash("sha256", random_int(0, 1000000) . time() . "carrito" . $this->viewData["mode"]);
        $_SESSION[XSR_KEY] = $this->viewData["xsrToken"];

        if (count($this->viewData["errores"]) > 0) {
            foreach ($this->viewData["errores"] as $campo => $mensaje) {
                $this->viewData["error_" . $campo] = $mensaje;
            }
        }
    }
}
