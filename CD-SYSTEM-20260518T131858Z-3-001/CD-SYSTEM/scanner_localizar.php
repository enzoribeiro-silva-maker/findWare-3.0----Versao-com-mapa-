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
<title>Localização por Scanner - FindWare</title>
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

.localizacao-destaque {
    background: #eef2ff;
    border: 1px solid #c7d2fe;
    color: #3730a3;
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 22px;
    font-weight: bold;
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

    <span>Localização por Scanner</span>
</div>

<div class="container">

<?php if($produto){ ?>

    <div class="card">

        <div class="titulo">
            <h2>Produto Localizado</h2>
            <p>O produto foi identificado pelo scanner. Confira abaixo a posição cadastrada no CD.</p>
        </div>

        <div class="info-produto">
            <p><strong>Produto:</strong> <?php echo $produto['nome']; ?></p>
            <p><strong>Código:</strong> <?php echo $codigo; ?></p>
            <p><strong>Estoque:</strong> <?php echo $produto['estoque']; ?></p>
        </div>

        <div class="localizacao-destaque">
            Localização:
            Corredor <?php echo $produto['corredor']; ?> -
            Prateleira <?php echo $produto['prateleira']; ?> -
            Nível <?php echo $produto['nivel']; ?>
        </div>

        <div class="acoes">
            <a href="mapa.php?busca=<?php echo $codigo; ?>" class="btn">
                Ver no Mapa
            </a>

            <a href="scanner.php" class="btn btn-secundario">
                Voltar ao Scanner
            </a>

            <a href="menu.php" class="btn btn-secundario">
                Voltar ao Menu
            </a>
        </div>

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

</html>