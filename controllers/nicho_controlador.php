<?php

require '../models/nicho_molde.php';

class Nicho_controlador
{

    private $nicho_molde;

    public function __construct()
    {
        $this->nicho_molde = new Nicho_molde();
    }

    public function crear_nicho_controlador($nombre, $tipo, $usuario_ID, $categorySlug, $descripcion, $url_imagen)
    {

        if ($this->nicho_molde->agregar_nicho($nombre, $tipo, $usuario_ID, $categorySlug, $descripcion, $url_imagen)) {
            echo json_encode([
                'status' => 'okey',
                'mensaje' => 'creado exitosamente',
                'URL' => $url_imagen,
            ]);
        } else {
            echo json_encode([
                'status' => 'Fallido',
                'mensaje' => 'No se a podido crear , intentelo mas tarde .'
            ]);
        }
    }

    public function editar_nicho_controlador($id, $nombre = null, $categorySlug= null, $tipo = null)
    {
        if ($this->nicho_molde->editar_nicho($id, $nombre, $categorySlug, $tipo)) {
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Nicho actualizado correctamente.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'No se pudo actualizar el nicho o no hay cambios.'
            ]);
        }
    }

    public function eliminar_nicho_controlador($id)
    {
        if ($this->nicho_molde->eliminar_nicho($id)) {
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Nicho eliminado correctamente.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Error al eliminar el nicho o no existe.'
            ]);
        }
    }

    public function obtener_nichos_controlador()
    {
        $nichos = $this->nicho_molde->obtener_nichos();
        if ($nichos && count($nichos) > 0) {
            echo json_encode([
                'status' => 'ok',
                'categorias' => $nichos
            ]);
        } else {
            echo json_encode([
                'status' => 'vacio',
                'mensaje' => 'No se encontraron categorías registradas.'
            ]);
        }
    }


}

?>