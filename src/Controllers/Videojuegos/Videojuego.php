<?php

namespace Controllers\Videojuegos;

use Controllers\PublicController;
use Dao\Videojuegos\Videojuegos as VideojuegosDAO;
use Utilities\Site;
use Utilities\Validators;
use Views\Renderer;

const LIST_URL = "index.php?page=Videojuegos-Videojuegos";
const XSR_KEY = "xsrToken_videojuegos";

class Videojuego extends PublicController
{
    private array $viewData;
    private array $modes;

    public function __construct()
    {
        $this->modes = [
            "INS" => 'Agregando un nuevo videojuego',
            "UPD" => 'Editando videojuego %s (%s)',
            "DEL" => 'Eliminando videojuego %s (%s)',
            "DSP" => 'Detalle del videojuego %s (%s)'
        ];

        $this->viewData = [
            "videojuegocod" => 0,
            "titulo" => "",
            "descripcion" => "",
            "precio" => "0.00",
            "imagen" => "",
            "archivo_descarga" => "",
            "formato" => "digital",
            "videojuegoest" => "ACT",
            "creado_en" => "",
            "mode" => "",
            "modeDsc" => "",
            "errores" => [],
            "readonly" => "",
            "showAction" => true,
            "xsrToken" => ""
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
        Renderer::render("Videojuegos/Videojuego", $this->viewData);
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
            if (isset($_GET["videojuegocod"])) {
                $this->viewData["videojuegocod"] = intval($_GET["videojuegocod"]);
                $videojuego = VideojuegosDAO::getVideojuegoById($this->viewData["videojuegocod"]);
                if ($videojuego) {
                    $this->viewData = array_merge($this->viewData, $videojuego);
                } else {
                    $this->throwError("No se encontró el videojuego solicitado.");
                }
            } else {
                $this->throwError("No se proporcionó el ID del videojuego.");
            }
        }
    }

    private function datosFormulario()
    {
        foreach (["titulo", "descripcion", "precio", "imagen", "archivo_descarga", "formato", "videojuegoest", "xsrToken"] as $campo) {
            if (isset($_POST[$campo])) {
                $this->viewData[$campo] = $_POST[$campo];
            }
        }
    }

    private function validarDatos()
    {
        if (Validators::IsEmpty($this->viewData["titulo"])) {
            $this->viewData["errores"]["titulo"] = "El título es obligatorio.";
        }
        if (!in_array($this->viewData["formato"], ["digital", "fisico", "ambos"])) {
            $this->viewData["errores"]["formato"] = "Formato inválido.";
        }
        if (!is_numeric($this->viewData["precio"]) || floatval($this->viewData["precio"]) < 0) {
            $this->viewData["errores"]["precio"] = "El precio debe ser un número positivo.";
        }
        $tmpXsrToken = $_SESSION[XSR_KEY];
        if ($this->viewData["xsrToken"] !== $tmpXsrToken) {
            error_log("Token inválido en videojuego.");
            $this->throwError("Solicitud inválida. Intente de nuevo.");
        }
    }

    private function procesarDatos()
    {
        switch ($this->viewData["mode"]) {
            case "INS":
                if (VideojuegosDAO::nuevoVideojuego(
                    $this->viewData["titulo"],
                    $this->viewData["descripcion"],
                    floatval($this->viewData["precio"]),
                    $this->viewData["imagen"],
                    $this->viewData["archivo_descarga"],
                    $this->viewData["formato"],
                    $this->viewData["videojuegoest"]
                ) > 0) {
                    Site::redirectToWithMsg(LIST_URL, "Videojuego agregado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al agregar el videojuego."];
                }
                break;
            case "UPD":
                if (VideojuegosDAO::actualizarVideojuego(
                    $this->viewData["videojuegocod"],
                    $this->viewData["titulo"],
                    $this->viewData["descripcion"],
                    floatval($this->viewData["precio"]),
                    $this->viewData["imagen"],
                    $this->viewData["archivo_descarga"],
                    $this->viewData["formato"],
                    $this->viewData["videojuegoest"]
                )) {
                    Site::redirectToWithMsg(LIST_URL, "Videojuego actualizado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al actualizar el videojuego."];
                }
                break;
            case "DEL":
                if (VideojuegosDAO::eliminarVideojuego($this->viewData["videojuegocod"])) {
                    Site::redirectToWithMsg(LIST_URL, "Videojuego eliminado exitosamente.");
                } else {
                    $this->viewData["errores"]["global"] = ["Error al eliminar el videojuego."];
                }
                break;
        }
    }

    private function prepararVista()
    {
        $this->viewData["modeDsc"] = sprintf(
            $this->modes[$this->viewData["mode"]],
            $this->viewData["titulo"],
            $this->viewData["videojuegocod"]
        );

        // Marcar formato seleccionado
        foreach (["digital", "fisico", "ambos"] as $formato) {
            $this->viewData["formato_" . $formato] = $this->viewData["formato"] === $formato ? "selected" : "";
        }

        // Validaciones visuales
        if (count($this->viewData["errores"]) > 0) {
            foreach ($this->viewData["errores"] as $campo => $error) {
                $this->viewData["error_" . $campo] = $error;
            }
        }

        // Control readonly y botón de acción
        if (in_array($this->viewData["mode"], ["DEL", "DSP"])) {
            $this->viewData["readonly"] = "readonly";
        }
        if ($this->viewData["mode"] === "DSP") {
            $this->viewData["showAction"] = false;
        }

        $this->viewData["xsrToken"] = hash("sha256", random_int(0, 1000000) . time() . 'videojuego' . $this->viewData["mode"]);
        $_SESSION[XSR_KEY] = $this->viewData["xsrToken"];
    }
}
