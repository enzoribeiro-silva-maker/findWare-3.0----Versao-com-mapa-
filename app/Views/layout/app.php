<?php
$rotaAtual = trim(str_replace(BASE_URL, '', parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');

function navAtiva(string $rota, string $atual): string
{
    return $atual === trim($rota, '/') ? 'ativo' : '';
}

$titulosRotas = [
    'menu' => ['Painel Principal', 'Sistema Inteligente de Localização de Produtos'],
    'dashboard' => ['Dashboard Completo', 'Visão geral de estoque e movimentações'],
    'produtos/cadastrar' => ['Cadastrar Produto', 'Registre um novo item no estoque'],
    'produtos/buscar' => ['Buscar Produto', 'Consulte produtos cadastrados'],
    'produtos/resultado' => ['Resultado da Busca', 'Produtos encontrados na pesquisa'],
    'produtos/localizar' => ['Localizar Produto', 'Encontre a posição de um produto no mapa do CD'],
    'produtos/perdido' => ['Produto Perdido', 'Busca por proximidade dentro do CD'],
    'produtos/excluir' => ['Excluir Produto', 'Remoção de item do cadastro'],
    'produtos/atualizar-localizacao' => ['Atualizar Localização', 'Novo endereço do produto'],
    'mapa' => ['Mapa do CD', 'Posição do produto no centro de distribuição'],
    'movimentacoes/recebimento' => ['Recebimento de Produtos', 'Registrar entrada no estoque'],
    'movimentacoes/saida' => ['Saída de Produtos', 'Registrar retirada do estoque'],
    'movimentacoes/historico' => ['Histórico de Movimentações', 'Entradas e saídas registradas'],
    'arduino' => ['Conexão Arduino', 'Conecte e monitore o leitor RFID via USB'],
    'scanner' => ['Scanner CD', 'Leitura de QR Code do produto'],
    'scanner/entrada' => ['Entrada por Scanner', 'Confirmar entrada de produto lido'],
    'scanner/saida' => ['Saída por Scanner', 'Confirmar saída de produto lido'],
    'scanner/localizar' => ['Localização por Scanner', 'Produto identificado pela leitura'],
];

[$tituloPagina, $subtituloPagina] = $titulosRotas[$rotaAtual] ?? ['FindWare', 'Sistema Inteligente de Localização de Produtos'];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($tituloPagina) ?> - FindWare</title>
<link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>

<div class="app-shell">

    <div id="overlayMenu" class="overlay" onclick="alternarMenuMobile()"></div>

    <aside class="sidebar" id="sidebar">

        <div class="sidebar-brand">
            <div class="logo-box">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 10.5 12 4l8 6.5V20a1 1 0 0 1-1 1h-4v-5H9v5H5a1 1 0 0 1-1-1z"/>
                </svg>
            </div>
            <div>
                <h1>FindWare</h1>
                <p>Localização Inteligente</p>
            </div>
        </div>

        <nav class="sidebar-nav">

            <div class="nav-titulo">Principal</div>
            <a href="<?= url('menu') ?>" class="nav-link <?= navAtiva('menu', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 10.8 12 4l8 6.8V20a1 1 0 0 1-1 1h-4v-6H9v6H5a1 1 0 0 1-1-1z"/></svg></span> Painel Principal</a>
            <a href="<?= url('dashboard') ?>" class="nav-link <?= navAtiva('dashboard', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 18V10M12 18V5M19 18v-7"/></svg></span> Dashboard</a>

            <div class="nav-titulo">Estoque</div>
            <a href="<?= url('produtos/cadastrar') ?>" class="nav-link <?= navAtiva('produtos/cadastrar', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg></span> Cadastrar Produto</a>
            <a href="<?= url('movimentacoes/recebimento') ?>" class="nav-link <?= navAtiva('movimentacoes/recebimento', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14"/><path d="m13 7 5 5-5 5"/><path d="M18 12H3"/></svg></span> Recebimento</a>
            <a href="<?= url('movimentacoes/saida') ?>" class="nav-link <?= navAtiva('movimentacoes/saida', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 12H6"/><path d="m11 7-5 5 5 5"/><path d="M5 12h14"/></svg></span> Saída de Produtos</a>
            <a href="<?= url('movimentacoes/historico') ?>" class="nav-link <?= navAtiva('movimentacoes/historico', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 5h14"/><path d="M5 12h14"/><path d="M5 19h8"/></svg></span> Movimentações</a>

            <div class="nav-titulo">Localização</div>
            <a href="<?= url('produtos/buscar') ?>" class="nav-link <?= navAtiva('produtos/buscar', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="5"/><path d="m15 15 4 4"/></svg></span> Buscar Produto</a>
            <a href="<?= url('produtos/localizar') ?>" class="nav-link <?= navAtiva('produtos/localizar', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 21s6-5.4 6-10a6 6 0 1 0-12 0c0 4.6 6 10 6 10Z"/><circle cx="12" cy="11" r="2"/></svg></span> Localizar no Mapa</a>
            <a href="<?= url('produtos/perdido') ?>" class="nav-link <?= navAtiva('produtos/perdido', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 2 19h20L12 3Z"/><path d="M12 8v5"/><path d="M12 16h.01"/></svg></span> Produto Perdido</a>
            <a href="<?= url('arduino') ?>" class="nav-link <?= navAtiva('arduino', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="7" width="10" height="10" rx="2"/><path d="M9 1v4M15 1v4M9 19v4M15 19v4M1 9h4M1 15h4M19 9h4M19 15h4"/></svg></span> Conexão Arduino</a>
            <a href="<?= url('scanner') ?>" class="nav-link <?= navAtiva('scanner', $rotaAtual) ?>"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="4" y="7" width="16" height="12" rx="2"/><path d="M9 7V5h6v2"/><path d="M9 13h6"/></svg></span> Scanner</a>

        </nav>

        <div class="sidebar-footer">
            <div class="sidebar-user">
                <div class="avatar"><?= e(mb_substr(usuarioLogado() ?? 'U', 0, 1)) ?></div>
                <div class="info">
                    <strong><?= e(usuarioLogado()) ?></strong>
                    <span>Operador do CD</span>
                </div>
            </div>
            <a href="<?= url('logout') ?>" class="btn-link"><span class="ic"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 17H6a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h4"/><path d="m13 7 4 5-4 5"/><path d="M17 12H9"/></svg></span> Sair do Sistema</a>
        </div>

    </aside>

    <div class="main">

        <header class="topbar">
            <button class="btn-menu-mobile" onclick="alternarMenuMobile()" type="button" aria-label="Abrir menu">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 7h16"/><path d="M4 12h16"/><path d="M4 17h16"/>
                </svg>
            </button>

            <div class="titulo-pagina">
                <?= e($tituloPagina) ?>
                <span><?= e($subtituloPagina) ?></span>
            </div>

            <button class="btn-tema" onclick="trocarTema()" type="button" title="Alternar tema" aria-label="Alternar tema">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M12 3v2"/><path d="M12 19v2"/><path d="M4.93 4.93l1.41 1.41"/><path d="M17.66 17.66l1.41 1.41"/><path d="M3 12h2"/><path d="M19 12h2"/><path d="M4.93 19.07l1.41-1.41"/><path d="M17.66 6.34l1.41-1.41"/><circle cx="12" cy="12" r="4"/>
                </svg>
            </button>
        </header>

        <main class="conteudo">
            <?php if (isset($conteudo) && is_callable($conteudo)) { $conteudo(); } ?>
        </main>

    </div>

</div>

<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
