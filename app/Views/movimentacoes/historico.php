<div class="page-header">
    <h2>Histórico de Movimentações</h2>
    <p>Consulte todas as entradas e saídas registradas no estoque.</p>
</div>

<div class="card">

    <div class="tabela-container">
        <table>
            <tr>
                <th>Produto</th>
                <th>Código</th>
                <th>Quantidade</th>
                <th>Tipo</th>
                <th>Localização / Motivo</th>
                <th>Data</th>
            </tr>

            <?php if (count($movimentacoes) > 0): ?>
                <?php foreach ($movimentacoes as $mov): ?>
                <tr>
                    <td><?= e($mov['produto']) ?></td>
                    <td style="color:var(--texto-suave); font-size:13px;"><?= e($mov['codigo_barras'] ?? '-') ?></td>
                    <td><?= (int) $mov['quantidade'] ?></td>
                    <td><span class="badge <?= strtolower($mov['tipo']) ?>"><?= e($mov['tipo']) ?></span></td>
                    <td><?= e($mov['localizacao']) ?></td>
                    <td><?= e($mov['data_movimentacao']) ?></td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="6" class="vazio">Nenhuma movimentação registrada até o momento.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="acoes">
        <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        <a href="<?= url('movimentacoes/recebimento') ?>" class="btn">Novo Recebimento</a>
        <a href="<?= url('movimentacoes/saida') ?>" class="btn btn-secundario">Nova Saída</a>
    </div>

</div>
