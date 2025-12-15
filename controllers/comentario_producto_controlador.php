<?php

require '../models/comentario_producto_molde.php';

class Comentario_producto_controlador
{
    private $comentario_molde;

    public function __construct()
    {
        $this->comentario_molde = new Comentario_producto_molde();
    }

    public function agregar_comentario_controlador($usuario_ID, $comentario, $producto_ID)
    {
        if ($this->comentario_molde->agregar_comentario($usuario_ID, $comentario, $producto_ID)) {
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Comentario agregado correctamente.'
            ]);
        } else {

            $error = $this->comentario_molde->getLastError();

            if ($error === 'duplicate') {
                echo json_encode(['status' => 'error', 'mensaje' => 'Ya has comentado este producto.']);
            } elseif ($error === 'no_rows') {
                echo json_encode(['status' => 'error', 'mensaje' => 'No se pudo agregar el comentario.']);
            } else {
                echo json_encode(['status' => 'error', 'mensaje' => 'Error inesperado: ' . $error]);
            }
        }
    }

    public function eliminar_comentario_controlador($comentario_ID, $usuario_ID, $es_admin)
    {
        if ($this->comentario_molde->eliminar_comentario($comentario_ID, $usuario_ID, $es_admin)) {
            echo json_encode([
                'status' => 'success',
                'mensaje' => 'Comentario eliminado correctamente.'
            ]);
        } else {
            $error = $this->comentario_molde->getLastError();
            $mensaje = match ($error) {
                'no_rows' => 'No se encontro el comentario o no tienes permiso para eliminarlo.',
                'invalid_id' => 'ID inválido proporcionado.',
                default => 'Error inesperado: ' . $error,
            };
            echo json_encode([
                'status' => 'error',
                'mensaje' => $mensaje
            ]);
        }
    }

}

?>