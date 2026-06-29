<?php
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Saída de Produtos - FindWare</title>
<link rel="stylesheet" href="assets/style.css">

<style>
.aviso-saida {
    background: #fff7ed;
    border: 1px solid #fed7aa;
    color: #9a3412;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 22px;
    font-size: 14px;
}

.btn-saida {
    background: #b91c1c;
}

.btn-saida:hover {
    background: #991b1b;
}
</style>
</head>

<body>

<div class="topo">
    <div>
        <h1>FindWare</h1>
        <span>Sistema Inteligente de Localização de Produtos</span>
    </div>

    <span>Saída de Produtos</span>
</div>

<div class="container">

    <div class="card">

        <div class="titulo">
            <h2>Registrar Saída de Produto</h2>
            <p>Registre a retirada de produtos do estoque por expedição, perda, transferência ou outro motivo.</p>
        </div>

        <div class="aviso-saida">
            Atenção: essa operação reduz a quantidade disponível no estoque.
        </div>

        <form action="salvar_saida.php" method="POST">

            <div class="form-grid">

                <div class="form-group">
                    <label>Código de barras / QR Code</label>
                    <input
                        type="text"
                        name="codigo_barras"
                        placeholder="Ex: 789123"
                        required>
                </div>

                <div class="form-group">
                    <label>Produto</label>
                    <input
                        type="text"
                        name="produto"
                        placeholder="Nome do produto"
                        required>
                </div>

                <div class="form-group">
                    <label>Quantidade</label>
                    <input
                        type="number"
                        name="quantidade"
                        placeholder="Ex: 5"
                        min="1"
                        required>
                </div>

                <div class="form-group">
                    <label>Motivo da saída</label>
                    <input
                        type="text"
                        name="motivo"
                        placeholder="Expedição, Perda, Transferência..."
                        required>
                </div>

            </div>

            <div class="acoes">
                <button type="submit" class="btn-saida">Registrar Saída</button>
                <a href="menu.php" class="btn btn-secundario">Voltar ao Menu</a>
            </div>

        </form>

    </div>

</div>
<script src="assets/tema.js"></script>
</body>

</html>