<?php
require '../models/pedido_comprar_molde.php';

class Pedido_controlador
{
    private $pedido_molde;

    public function __construct()
    {
        $this->pedido_molde = new Pedido_molde();
    }

    public function crear_desde_carrito($usuario_ID)
    {
        $pedido_ID = $this->pedido_molde->crear_desde_carrito($usuario_ID);
        if ($pedido_ID) {
            echo json_encode([
                'status' => 'success',
                'mensaje' => 'Pedido creado correctamente desde el carrito.',
                'pedido_ID' => $pedido_ID
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Error: ' . $this->pedido_molde->getLastError()
            ]);
        }
    }

    public function crear_pedido_directo_controlador($usuario_ID, $producto_ID, $cantidad)
    {
        $pedido_ID = $this->pedido_molde->crear_pedido_directo($usuario_ID, $producto_ID, $cantidad);
        if ($pedido_ID) {
            echo json_encode([
                'status' => 'success',
                'mensaje' => 'Pedido creado correctamente (compra directa).',
                'pedido_ID' => $pedido_ID
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'mensaje' => 'Error: ' . $this->pedido_molde->getLastError()
            ]);
        }
    }

    public function obtener_pedidos_por_usuario_controlador($usuario_ID)
    {
        $pedidos = $this->pedido_molde->obtener_pedidos_por_usuario($usuario_ID);
        echo json_encode([
            'status' => 'success',
            'pedidos' => $pedidos
        ]);
    }

    public function obtener_carritos_por_usuario_controlador($usuario_ID)
    {
        $pedidos = $this->pedido_molde->obtener_carritos_por_usuario($usuario_ID);
        echo json_encode([
            'status' => 'success',
            'pedidos' => $pedidos
        ]);
    }
}
?>