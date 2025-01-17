<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once dirname(__file__) . '/../config/config.inc.php';

define('TOKEN', '601Oc07U1W');

class CarritoVacio
{
    public $carrito;

    public function __construct()
    {
        if (!isset($_GET['token']) || $_GET['token'] !== TOKEN) {
            die("TODO MAL.");
        }
        $datos = Cart::getNonOrderedCarts('2024-12-01 00:00:00', '2024-12-20 23:59:59');
        $this->crearTXT($datos);
        //$this->enviarMail($datos);    
        
        dump($datos);

        $datosNecesarios = [];

        foreach ($datos as $cartVacio) {
            if (isset($cartVacio['id_cart'])) {
                $datosNecesarios[] = (string)$cartVacio['id_cart'];
            }
        }
        print_r($datosNecesarios);

        if (count($datosNecesarios)>0) {

            $idsMensaje = "Carritos vacíos: ";

            foreach ($datosNecesarios as $idCarrito) {
                $idsMensaje .= $idCarrito . ', ';
            }
            PrestaShopLogger::addLog(
                $idsMensaje,
                1,
                null,
                'Mail',
                0,
                false,
                1
            );
        }

        if ($datosNecesarios) {
            $idsMensajes = implode(', ', $datosNecesarios);

            PrestaShopLogger::addLog(
                "Carritos vacíos: " . $idsMensajes,
                4,
                null,
                'Mail',
                0,
                false,
                1
            );
        }
    }

    public function crearTXT($datos)
    {
        $file = dirname(__file__) . '/cron.txt';
        file_put_contents($file, print_r($datos, true), FILE_APPEND | LOCK_EX);
    }

    public function enviarMail($datos)
    {
        foreach ($datos as $carrito) {
            $id_cliente = $carrito['id_customer'];
            $cliente = new Customer($id_cliente);

            if (Validate::isLoadedObject($cliente)) {
                var_dump($cliente->email);

                $template_vars = [
                    'nombre' => $cliente->firstname,
                ];

                if (Validate::isEmail($cliente->email)) {
                    $template_path = dirname(__file__) . '/mails';
                    var_dump($template_path);

                    $mailEnviat = Mail::Send(
                        1,
                        "plantilla",
                        "recordatorio",
                        $template_vars,
                        $cliente->email,
                        null,
                        null,
                        null,
                        null,
                        null,
                        $template_path
                    );
                }
            }
        }
    }
}
$c = new CarritoVacio();
