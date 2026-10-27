<div class="page-header">
    <h2>Confirmar Entrada de Produto</h2>
    <p>Complete os dados do produto lido pelo scanner e registre a entrada no estoque.</p>
</div>

<div class="card">

    <form action="<?= url('movimentacoes/recebimento') ?>" method="POST">

        <div class="form-grid">

            <div class="form-group">
                <label>Código de barras / QR Code</label>
                <input type="text" name="codigo_barras" value="<?= e($codigo) ?>" placeholder="Código do produto" required>
            </div>

            <div class="form-group">
                <label>Produto</label>
                <input type="text" name="produto" placeholder="Nome do produto" required>
            </div>

            <div class="form-group">
                <label>Quantidade recebida</label>
                <input type="number" name="quantidade" placeholder="Ex: 10" min="1" required>
            </div>

            <div class="form-group">
                <label>Corredor</label>
                <input type="text" name="corredor" placeholder="Ex: A" required>
            </div>

            <div class="form-group">
                <label>Prateleira</label>
                <input type="number" name="prateleira" placeholder="Ex: 3" min="1" required>
            </div>

            <div class="form-group">
                <label>Nível</label>
                <input type="number" name="nivel" placeholder="Ex: 2" min="1" required>
            </div>

        </div>

        <div class="acoes">
            <button type="submit">Confirmar Entrada</button>
            <a href="<?= url('scanner') ?>" class="btn btn-secundario">Voltar ao Scanner</a>
            <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </form>

</div>
