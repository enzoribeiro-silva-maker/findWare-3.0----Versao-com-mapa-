<?php

include "conectar.php";

$codigo = $_GET['codigo'] ?? "";

$sql = "SELECT * FROM produtos WHERE codigo_barras='$codigo' OR codigo='$codigo'";
$resultado = mysqli_query($conn, $sql);
$produto = mysqli_fetch_assoc($resultado);

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Saída por Scanner - FindWare</title>
<link rel="stylesheet" href="assets/style.css">

<style>
.info-produto {
    background: #f9fafb;
    border: 1px solid #d5dbe3;
    border-radius: 8px;
    padding: 18px;
    margin-bottom: 22px;
}

.info-produto p {
    margin-bottom: 10px;
    color: #374151;
}

.info-produto strong {
    color: #111827;
}

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

.erro-box {
    max-width: 620px;
}
</style>
</head>

<body>

<div class="topo">
    <div>
        <h1>FindWare</h1>
        <span>Sistema Inteligente de Localização de Produtos</span>
    </div>

    <span>Saída por Scanner</span>
</div>

<div class="container">

<?php if($produto){ ?>

    <div class="card">

        <div class="titulo">
            <h2>Confirmar Saída de Produto</h2>
            <p>Produto identificado pelo scanner. Informe a quantidade e o motivo da saída.</p>
        </div>

        <div class="info-produto">
            <p><strong>Produto:</strong> <?php echo $produto['nome']; ?></p>
            <p><strong>Código:</strong> <?php echo $codigo; ?></p>
            <p><strong>Estoque atual:</strong> <?php echo $produto['estoque']; ?></p>
            <p>
                <strong>Localização:</strong>
                Corredor <?php echo $produto['corredor']; ?> -
                Prateleira <?php echo $produto['prateleira']; ?> -
                Nível <?php echo $produto['nivel']; ?>
            </p>
        </div>

        <div class="aviso-saida">
            Atenção: essa operação irá reduzir o estoque atual do produto.
        </div>

        <form action="salvar_saida.php" method="POST">

            <input type="hidden" name="codigo_barras" value="<?php echo $codigo; ?>">
            <input type="hidden" name="produto" value="<?php echo $produto['nome']; ?>">

            <div class="form-grid">

                <div class="form-group">
                    <label>Quantidade para saída</label>
                    <input 
                        type="number" 
                        name="quantidade" 
                        placeholder="Ex: 2" 
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
                <button type="submit" class="btn-saida">Confirmar Saída</button>
                <a href="scanner.php" class="btn btn-secundario">Voltar ao Scanner</a>
                <a href="menu.php" class="btn btn-secundario">Voltar ao Menu</a>
            </div>

        </form>

    </div>

<?php } else { ?>

    <div class="card erro-box">

        <div class="titulo">
            <h2>Produto não encontrado</h2>
            <p>Nenhum produto foi encontrado com o código lido pelo scanner.</p>
        </div>

        <div class="acoes">
            <a href="scanner.php" class="btn">Tentar Novamente</a>
            <a href="menu.php" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </div>

<?php } ?>

</div>
<script src="assets/tema.js"></script>
</body>
<?php include "includes/topo.php"; ?>
</html>