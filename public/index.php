<?php

/**
 * FindWare - Sistema Inteligente de Localização de Produtos
 * Front Controller: toda requisição passa por aqui.
 */

session_start();

define('BASE_URL', rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/'));
define('APP_PATH', dirname(__DIR__) . '/app');

require_once APP_PATH . '/helpers.php';
require_once APP_PATH . '/Core/Database.php';
require_once APP_PATH . '/Core/Controller.php';
require_once APP_PATH . '/Core/Router.php';

require_once APP_PATH . '/Controllers/AuthController.php';
require_once APP_PATH . '/Controllers/DashboardController.php';
require_once APP_PATH . '/Controllers/ProdutoController.php';
require_once APP_PATH . '/Controllers/MovimentacaoController.php';
require_once APP_PATH . '/Controllers/MapaController.php';
require_once APP_PATH . '/Controllers/ScannerController.php';
require_once APP_PATH . '/Controllers/ArduinoController.php';

$router = new Router();
require APP_PATH . '/routes.php';
$router->despachar();
