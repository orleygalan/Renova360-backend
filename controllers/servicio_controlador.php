<?php

require '../models/servicio_molde.php';

class Servicio_controlador
{
    private $servicio_molde;

    public function __construct()
    {
        $this->servicio_molde = new Servicio_molde();
    }

    
    public function crear_servicio_controlador($descripcion_nombre, $url_imagen, $categoria_ID, $nombre_empresa)
    {
        
        $url_imagen = is_array($url_imagen) ? $url_imagen[0] : $url_imagen;

        $resultado = $this->servicio_molde->crear_servicio($descripcion_nombre, $url_imagen, $categoria_ID, $nombre_empresa);

        if ($resultado) {
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Servicio creado exitosamente.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'No se pudo crear el servicio, intenta más tarde.'
            ]);
        }
    }

    public function obtener_servicios_controlador()
    {
        $servicios = $this->servicio_molde->obtener_servicios();

        if (!empty($servicios)) {
            echo json_encode([
                'status' => 'ok',
                'servicios' => $servicios
            ]);
        } else {
            echo json_encode([
                'status' => 'vacio',
                'mensaje' => 'No hay servicios registrados.'
            ]);
        }
    }

    public function editar_servicio_controlador($data)
    {
        if ($this->servicio_molde->editar_servicio($data)) {
            echo json_encode(['status' => 'ok', 'mensaje' => 'Servicio actualizado']);
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'Error al actualizar el servicio']);
        }
    }

    public function eliminar_servicio_controlador($servicio_ID)
    {
        if ($this->servicio_molde->eliminar_servicio($servicio_ID)) {
            echo json_encode(['status' => 'ok', 'mensaje' => 'Servicio eliminado']);
        } else {
            echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo eliminar el servicio']);
        }
    }
}

?>