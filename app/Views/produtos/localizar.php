<div class="page-header">
    <h2>Localizar Produto no CD</h2>
    <p>Busque um produto pelo nome, código interno, código de barras ou QR Code.</p>
</div>

<div class="card" style="max-width:720px;">

    <form action="<?= url('mapa') ?>" method="GET">

        <label>Produto ou código</label>

        <div class="campo-busca">
            <input type="text" name="busca" placeholder="Digite o código ou nome do produto" required>
            <button type="submit">Buscar Produto</button>
        </div>

    </form>

    <div class="aviso aviso-azul" style="margin-top:22px;">
        Digite o código cadastrado no produto ou o nome do item. Após buscar, o sistema abrirá o mapa indicando a posição do produto no CD.
    </div>

    <div class="acoes">
        <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
    </div>

</div>
