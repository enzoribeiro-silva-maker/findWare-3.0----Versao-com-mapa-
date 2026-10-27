<?php
$totalProdutos = $totalProdutos ?? 0;
$totalEstoque = $totalEstoque ?? 0;
$entradas = $entradas ?? 0;
$saidas = $saidas ?? 0;
$estoqueBaixo = $estoqueBaixo ?? 0;
?>

<div class="page-header">
    <h2>Olá, <?= e(usuarioLogado()) ?> </h2>
    <p>Controle de estoque, localização, recebimento, saída e movimentações do centro de distribuição.</p>
</div>

<div class="stat-grid">

    <div class="stat-card azul">
        <div>
            <h3>Produtos cadastrados</h3>
            <div class="numero"><?= $totalProdutos ?></div>
        </div>
    </div>

    <div class="stat-card azul">
        <div>
            <h3>Estoque total</h3>
            <div class="numero"><?= $totalEstoque ?></div>
        </div>
    </div>

    <div class="stat-card verde">
        <div>
            <h3>Entradas</h3>
            <div class="numero entrada"><?= $entradas ?></div>
        </div>
    </div>

    <div class="stat-card vermelho">
        <div>
            <h3>Saídas</h3>
            <div class="numero saida"><?= $saidas ?></div>
        </div>
    </div>

    <div class="stat-card laranja">
        <div>
            <h3>Estoque baixo</h3>
            <div class="numero alerta"><?= $estoqueBaixo ?></div>
        </div>
    </div>

</div>

<div class="card">

    <div class="page-header" style="margin-bottom:18px;">
        <h2>Operações do Sistema</h2>
        <p>Escolha uma funcionalidade para continuar.</p>
    </div>

    <div class="menu-operacoes">

        <a class="item-menu" href="<?= url('dashboard') ?>">
            <div class="icone">◫</div>
            <div><strong>Dashboard Completo</strong><span>Visão geral e listagem de produtos</span></div>
        </a>

        <a class="item-menu" href="<?= url('movimentacoes/recebimento') ?>">
            <div class="icone">↗</div>
            <div><strong>Recebimento</strong><span>Registrar entrada de produtos</span></div>
        </a>

        <a class="item-menu" href="<?= url('movimentacoes/saida') ?>">
            <div class="icone">↘</div>
            <div><strong>Saída de Produtos</strong><span>Registrar retirada de estoque</span></div>
        </a>

        <a class="item-menu" href="<?= url('produtos/cadastrar') ?>">
            <div class="icone">+</div>
            <div><strong>Cadastrar Produto</strong><span>Adicionar novo item ao estoque</span></div>
        </a>

        <a class="item-menu" href="<?= url('produtos/buscar') ?>">
            <div class="icone">⌕</div>
            <div><strong>Buscar Produto</strong><span>Pesquisar por nome ou código</span></div>
        </a>

        <a class="item-menu" href="<?= url('produtos/localizar') ?>">
            <div class="icone">⌖</div>
            <div><strong>Localizar Produto</strong><span>Ver posição no mapa do CD</span></div>
        </a>

        <a class="item-menu" href="<?= url('produtos/perdido') ?>">
            <div class="icone">!</div>
            <div><strong>Produto Perdido</strong><span>Busca por proximidade</span></div>
        </a>

        <a class="item-menu" href="<?= url('movimentacoes/historico') ?>">
            <div class="icone">▤</div>
            <div><strong>Movimentações</strong><span>Histórico de entradas e saídas</span></div>
        </a>

        <a class="item-menu" href="<?= url('arduino') ?>">
            <div class="icone">⌁</div>
            <div><strong>Conexão Arduino</strong><span>Conectar USB, testar RFID e acompanhar mensagens</span></div>
        </a>

        <a class="item-menu" href="<?= url('scanner') ?>">
            <div class="icone">◧</div>
            <div><strong>Scanner</strong><span>Leitura de QR Code</span></div>
        </a>

    </div>

</div>
