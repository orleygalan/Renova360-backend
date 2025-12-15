<?php
require_once '../models/producto_molde.php';

class Producto_controlador
{
    private $producto_molde;

    public function __construct()
    {
        $this->producto_molde = new Producto_molde();
    }

    public function crear_producto_controlador(
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $categoria_ID,
        $colores,
        $dimensiones,
        $urls_imagenes
    ) {

        // asegurar que siemmpre sea un arrao
        $urls_imagenes = is_array($urls_imagenes) ? $urls_imagenes : [$urls_imagenes];

        $resultado = $this->producto_molde->agregar_producto_completo(
            $nombre,
            $descripcion,
            $precio,
            $stock,
            $categoria_ID,
            $colores,
            $dimensiones,
            $urls_imagenes
        );

        if ($resultado) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Producto creado correctamente.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se pudo crear el producto. Verifica los datos enviados.'
            ]);
        }
    }

    public function editar_producto_controlador($producto_ID, $datos)
    {
        $resultado = $this->producto_molde->editar_producto_completo($producto_ID, $datos);

        if ($resultado) {
            echo json_encode(['status' => 'success', 'message' => 'Producto actualizado correctamente.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error al actualizar el producto.']);
        }
    }


    public function eliminar_producto_controlador($producto_id)
    {
        if ($this->producto_molde->eliminar_producto($producto_id)) {
            return [
                'status' => 'success',
                'message' => 'Producto eliminado correctamente.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Error al eliminar el producto.'
            ];
        }
    }

    public function obtener_productos_controlador()
    {
        $productos = $this->producto_molde->obtener_productos_completos();

        if (!empty($productos)) {
            echo json_encode([
                'status' => 'success',
                'data' => $productos
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'No se encontraron productos.'
            ]);
        }
    }

}
?>
