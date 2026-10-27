<?php if ($produto): ?>

    <div class="page-header">
        <h2>Confirmar Saída de Produto</h2>
        <p>Produto identificado pelo scanner. Informe a quantidade e o motivo da saída.</p>
    </div>

    <div class="card">

        <div class="info-produto" style="margin-bottom:20px;">
            <p><strong>Produto:</strong> <?= e($produto['nome']) ?></p>
            <p><strong>Código:</strong> <?= e($codigo) ?></p>
            <p><strong>Estoque atual:</strong> <?= (int) $produto['estoque'] ?></p>
            <p><strong>Localização:</strong> Corredor <?= e($produto['corredor']) ?> - Prateleira <?= e($produto['prateleira']) ?> - Nível <?= e($produto['nivel']) ?></p>
        </div>

        <div class="aviso aviso-laranja">
            Atenção: essa operação irá reduzir o estoque atual do produto.
        </div>

        <form action="<?= url('movimentacoes/saida') ?>" method="POST">

            <input type="hidden" name="codigo_barras" value="<?= e($codigo) ?>">
            <input type="hidden" name="produto" value="<?= e($produto['nome']) ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Quantidade para saída</label>
                    <input type="number" name="quantidade" placeholder="Ex: 2" min="1" required>
                </div>
                <div class="form-group">
                    <label>Motivo da saída</label>
                    <input type="text" name="motivo" placeholder="Expedição, Perda, Transferência..." required>
                </div>
            </div>

            <div class="acoes">
                <button type="submit" class="btn-saida">Confirmar Saída</button>
                <a href="<?= url('scanner') ?>" class="btn btn-secundario">Voltar ao Scanner</a>
                <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
            </div>

        </form>

    </div>

<?php else: ?>

    <div class="card" style="max-width:620px;">
        <div class="page-header">
            <h2>Produto não encontrado</h2>
            <p>Nenhum produto foi encontrado com o código lido pelo scanner.</p>
        </div>
        <div class="acoes">
            <a href="<?= url('scanner') ?>" class="btn">Tentar Novamente</a>
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>
    </div>

<?php endif; ?>
