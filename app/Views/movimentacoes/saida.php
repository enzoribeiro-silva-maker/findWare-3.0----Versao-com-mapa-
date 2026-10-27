<div class="page-header">
    <h2>Registrar Saída de Produto</h2>
    <p>Registre a retirada de produtos do estoque por expedição, perda, transferência ou outro motivo.</p>
</div>

<div class="card">

    <div class="aviso aviso-laranja">
        Atenção: essa operação reduz a quantidade disponível no estoque.
    </div>

    <?php if ($produto): ?>
        <div class="info-produto" style="margin-bottom:22px;">
            <p><strong>Produto:</strong> <?= e($produto['nome']) ?></p>
            <p><strong>Estoque atual:</strong> <?= (int) $produto['estoque'] ?></p>
        </div>
    <?php endif; ?>

    <form action="<?= url('movimentacoes/saida') ?>" method="POST">

        <div class="form-grid">

            <div class="form-group">
                <label>Código de barras / QR Code</label>
                <input type="text" name="codigo_barras" placeholder="Ex: 789123" value="<?= e($codigo ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Produto</label>
                <input type="text" name="produto" placeholder="Nome do produto" value="<?= e($produto['nome'] ?? '') ?>" required>
            </div>

            <div class="form-group">
                <label>Quantidade</label>
                <input type="number" name="quantidade" placeholder="Ex: 5" min="1" required>
            </div>

            <div class="form-group">
                <label>Motivo da saída</label>
                <input type="text" name="motivo" placeholder="Expedição, Perda, Transferência..." required>
            </div>

        </div>

        <div class="acoes">
            <button type="submit" class="btn-saida">Registrar Saída</button>
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </form>

</div>
