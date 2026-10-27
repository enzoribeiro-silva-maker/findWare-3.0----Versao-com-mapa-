<div class="page-header">
    <h2>Resultado da Busca</h2>
    <p>Busca realizada por: <strong><?= e($busca) ?></strong></p>
</div>

<div class="card">

    <div class="tabela-container">
        <table>
            <tr>
                <th>Produto</th>
                <th>Código</th>
                <th>Código de Barras</th>
                <th>Corredor</th>
                <th>Prateleira</th>
                <th>Nível</th>
                <th>Estoque</th>
                <th>Ações</th>
            </tr>

            <?php if (count($produtos) > 0): ?>
                <?php foreach ($produtos as $produto): ?>
                <tr>
                    <td><?= e($produto['nome']) ?></td>
                    <td><?= e($produto['codigo']) ?></td>
                    <td><?= e($produto['codigo_barras']) ?></td>
                    <td><?= e($produto['corredor']) ?></td>
                    <td><?= e($produto['prateleira']) ?></td>
                    <td><?= e($produto['nivel']) ?></td>
                    <td>
                        <span class="badge-estoque <?= $produto['estoque'] <= 5 ? 'estoque-baixo' : 'estoque-ok' ?>">
                            <?= (int) $produto['estoque'] ?>
                        </span>
                    </td>
                    <td>
                        <a href="<?= url('mapa?busca=' . urlencode($produto['codigo'])) ?>" class="btn" style="padding:8px 14px;font-size:13px;">Ver Mapa</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="8" class="vazio">Nenhum produto encontrado com essa busca.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="acoes">
        <a href="<?= url('produtos/buscar') ?>" class="btn">Nova Busca</a>
        <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
    </div>

</div>
