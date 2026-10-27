<div class="page-header">
    <h2>Buscar Produto</h2>
    <p>Consulte um produto cadastrado pelo nome, código interno ou código de barras.</p>
</div>

<div class="card" style="max-width:720px;">

    <form action="<?= url('produtos/resultado') ?>" method="GET">

        <label>Nome ou código do produto</label>

        <div class="campo-busca">
            <input type="text" name="busca" placeholder="Digite o nome ou código" required>
            <button type="submit">Buscar</button>
        </div>

    </form>

    <div class="aviso aviso-azul" style="margin-top:22px;">
        <strong>Dica:</strong> você pode pesquisar por nome do produto, código interno, código de barras ou QR Code.
        Para localizar no mapa, use a tela "Localizar Produto".
    </div>

    <div class="acoes">
        <a href="<?= url('menu') ?>" class="btn btn-secundario">Voltar ao Menu</a>
    </div>

</div>
