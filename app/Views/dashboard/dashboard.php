<?php
$totalProdutos = $totalProdutos ?? 0;
$totalEstoque = $totalEstoque ?? 0;
$entradas = $entradas ?? 0;
$saidas = $saidas ?? 0;
$estoqueBaixo = $estoqueBaixo ?? 0;
$produtos = $produtos ?? [];
$ultimasMovimentacoes = $ultimasMovimentacoes ?? [];
?>

<div class="page-header">
    <h2>Dashboard do Centro de Distribuição</h2>
    <p>Visão geral dos produtos, estoque e movimentações do FindWare.</p>
</div>

<div class="stat-grid">

    <div class="stat-card azul">
        <div><h3>Total de Produtos</h3><div class="numero"><?= $totalProdutos ?></div></div>
    </div>

    <div class="stat-card azul">
        <div><h3>Total em Estoque</h3><div class="numero"><?= $totalEstoque ?></div></div>
    </div>

    <div class="stat-card verde">
        <div><h3>Entradas</h3><div class="numero entrada"><?= $entradas ?></div></div>
    </div>

    <div class="stat-card vermelho">
        <div><h3>Saídas</h3><div class="numero saida"><?= $saidas ?></div></div>
    </div>

    <div class="stat-card laranja">
        <div><h3>Estoque Baixo</h3><div class="numero alerta"><?= $estoqueBaixo ?></div></div>
    </div>

</div>

<div class="card">

    <div class="page-header" style="margin-bottom:18px;">
        <h2>Produtos cadastrados</h2>
        <p>Últimos itens cadastrados no sistema.</p>
    </div>

    <div class="tabela-container">
        <table>
            <tr>
                <th>Produto</th>
                <th>Código</th>
                <th>QR Code</th>
                <th>Localização</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>

            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= e($produto['nome']) ?></td>
                    <td><?= e($produto['codigo']) ?></td>
                    <td><?= e($produto['codigo_barras']) ?></td>
                    <td>
                        <?= e(strtoupper($produto['corredor']) . '-' . str_pad($produto['prateleira'], 2, '0', STR_PAD_LEFT) . '-N' . $produto['nivel']) ?>
                    </td>
                    <td>
                        <span class="badge-estoque <?= $produto['estoque'] <= 5 ? 'estoque-baixo' : 'estoque-ok' ?>">
                            <?= (int) $produto['estoque'] ?>
                        </span>
                    </td>
                    <td>
                        <div class="acoes-tabela">
                            <a href="<?= url('mapa?busca=' . urlencode($produto['codigo_barras'] ?? $produto['codigo'])) ?>" class="btn" style="padding:8px 14px;font-size:13px;">Mapa</a>
                            <a href="<?= url('produtos/excluir?id=' . $produto['id']) ?>" class="btn btn-secundario" style="padding:8px 14px;font-size:13px;" onclick="return confirm('Excluir este produto?')">Excluir</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="vazio">Nenhum produto cadastrado.</td></tr>
            <?php endif; ?>
        </table>
    </div>

</div>

<div class="card">

    <div class="page-header" style="margin-bottom:18px;">
        <h2>Últimas Movimentações</h2>
        <p>Resumo das últimas entradas e saídas registradas.</p>
    </div>

    <div class="tabela-container">
        <table>
            <tr>
                <th>Produto</th>
                <th>Quantidade</th>
                <th>Tipo</th>
                <th>Localização / Motivo</th>
                <th>Data</th>
            </tr>

            <?php if (count($ultimasMovimentacoes) > 0): ?>
                <?php foreach ($ultimasMovimentacoes as $mov): ?>
                <tr>
                    <td><?= e($mov['produto']) ?></td>
                    <td><?= (int) $mov['quantidade'] ?></td>
                    <td><span class="badge <?= strtolower($mov['tipo']) ?>"><?= e($mov['tipo']) ?></span></td>
                    <td><?= e($mov['localizacao']) ?></td>
                    <td><?= e($mov['data_movimentacao']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="5" class="vazio">Nenhuma movimentação registrada.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="acoes">
        <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        <a href="<?= url('movimentacoes/historico') ?>" class="btn">Ver Histórico Completo</a>
    </div>

</div>
