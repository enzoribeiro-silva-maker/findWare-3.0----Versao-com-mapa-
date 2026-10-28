
<?php
/** @var Router $router */

$router->get('/', function () {
    redirecionar(usuarioLogado() ? 'menu' : 'login');
});

// Autenticação
$router->get('login', ['AuthController', 'loginForm']);
$router->post('login', ['AuthController', 'login']);
$router->get('logout', ['AuthController', 'logout']);
$router->get('cadastro', ['AuthController', 'cadastroForm']);
$router->post('cadastro', ['AuthController', 'cadastrar']);

// Painel / Dashboard
$router->get('menu', ['DashboardController', 'menu']);
$router->get('dashboard', ['DashboardController', 'dashboard']);

// Produtos
$router->get('produtos/cadastrar', ['ProdutoController', 'cadastrarForm']);
$router->post('produtos/salvar', ['ProdutoController', 'salvar']);
$router->get('produtos/buscar', ['ProdutoController', 'buscarForm']);
$router->get('produtos/resultado', ['ProdutoController', 'resultado']);
$router->get('produtos/localizar', ['ProdutoController', 'localizarForm']);
$router->get('produtos/perdido', ['ProdutoController', 'perdidoForm']);
$router->post('produtos/atualizar-localizacao', ['ProdutoController', 'atualizarLocalizacao']);
$router->get('produtos/excluir', ['ProdutoController', 'excluir']);
$router->get('produtos/buscar-codigo', ['ProdutoController', 'buscarPorCodigoJson']);
$router->get('produtos/listar-json', ['ProdutoController', 'listarJson']);

// RFID: consultar tag e vincular ao produto
$router->get('produtos/buscar-rfid', ['ProdutoController', 'buscarPorRfidJson']);
$router->post('produtos/vincular-rfid', ['ProdutoController', 'vincularRfid']);

// Mapa do Centro de Distribuição
$router->get('mapa', ['MapaController', 'index']);

// Movimentações (entrada/saída de estoque)
$router->get('movimentacoes/recebimento', ['MovimentacaoController', 'recebimentoForm']);
$router->post('movimentacoes/recebimento', ['MovimentacaoController', 'salvarRecebimento']);
$router->get('movimentacoes/saida', ['MovimentacaoController', 'saidaForm']);
$router->post('movimentacoes/saida', ['MovimentacaoController', 'salvarSaida']);
$router->get('movimentacoes/historico', ['MovimentacaoController', 'historico']);

// Conexão com Arduino via USB/Serial (Web Serial API)
$router->get('arduino', ['ArduinoController', 'index']);

// Scanner de QR Code
$router->get('scanner', ['ScannerController', 'index']);
$router->get('scanner/entrada', ['ScannerController', 'entrada']);
$router->get('scanner/saida', ['ScannerController', 'saida']);
$router->get('scanner/localizar', ['ScannerController', 'localizar']);
