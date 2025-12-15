<?php

require '../models/carrito_molde.php';

class Carrito_controlador
{

    private $carrito_molde;

    public function __construct()
    {
        $this->carrito_molde = new Carrito_molde();
    }

    public function agregar_producto_carrito_controlador($usuario_ID, $producto_ID)
    {
        if ($this->carrito_molde->agregar_producto_carrito($usuario_ID, $producto_ID)) {
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Producto agregado correctamente al carrito .'
            ]);
        } else {
            echo json_encode([
                'status' => 'Fallido',
                'mensaje' => 'No se a podido agregar el producto al carro , intentelo mas tarde .'
            ]);
        }
    }

    public function eliminar_producto_carrito_controlador($usuario_ID, $producto_ID)
    {
        if ($this->carrito_molde->eliminar_producto_carrito($usuario_ID, $producto_ID)) {
            echo json_encode([
                'status' => 'ok',
                'mensaje' => 'Producto eliminado correctamente del carrito.'
            ]);
        } else {
            echo json_encode([
                'status' => 'fallido',
                'mensaje' => 'No se pudo eliminar el producto del carrito, inténtelo mas tarde.'
            ]);
        }
    }


}


?>