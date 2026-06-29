<?php

include "conectar.php";

$nome = trim($_POST['nome']);
$codigo = trim($_POST['codigo']);
$codigo_barras = trim($_POST['codigo_barras']);
$estoque = intval($_POST['estoque']);
$corredor = strtoupper(trim($_POST['corredor']));
$prateleira = trim($_POST['prateleira']);
$nivel = trim($_POST['nivel']);

if (
    empty($nome) ||
    empty($codigo) ||
    empty($codigo_barras) ||
    empty($corredor) ||
    empty($prateleira) ||
    empty($nivel)
) {
    $status = "erro";
    $titulo = "Campos obrigatórios";
    $mensagem = "Preencha todos os campos antes de cadastrar o produto.";
} else {

    $sql = "INSERT INTO produtos
    (
        nome,
        codigo,
        codigo_barras,
        corredor,
        prateleira,
        nivel,
        estoque
    )
    VALUES
    (
        '$nome',
        '$codigo',
        '$codigo_barras',
        '$corredor',
        '$prateleira',
        '$nivel',
        '$estoque'
    )";

    if (mysqli_query($conn, $sql)) {
        $status = "sucesso";
        $titulo = "Produto cadastrado com sucesso!";
        $mensagem = "O produto $nome foi registrado no estoque do FindWare.";
    } else {
        $status = "erro";
        $titulo = "Erro ao cadastrar produto";
        $mensagem = mysqli_error($conn);
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro de Produto - FindWare</title>
<link rel="stylesheet" href="assets/style.css">

<style>
.resultado {
    min-height: calc(100vh - 72px);
    display: flex;
    align-items: center;
    justify-content: center;
}

.resultado-card {
    width: 100%;
    max-width: 560px;
    text-align: center;
}

.icone-status {
    width: 66px;
    height: 66px;
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 32px;
    font-weight: bold;
}

.icone-status.sucesso {
    background: #dcfce7;
    color: #166534;
}

.icone-status.erro {
    background: #fee2e2;
    color: #991b1b;
}

.resumo {
    background: #f9fafb;
    border: 1px solid #d5dbe3;
    border-radius: 8px;
    padding: 16px;
    margin: 20px 0;
    text-align: left;
}

.resumo p {
    margin-bottom: 8px;
    color: #374151;
}

.resumo strong {
    color: #111827;
}
</style>
</head>

<body>

<div class="topo">
    <div>
        <h1>FindWare</h1>
        <span>Sistema Inteligente de Localização de Produtos</span>
    </div>

    <span>Cadastro de Produto</span>
</div>

<div class="container resultado">

    <div class="card resultado-card">

        <div class="icone-status <?php echo $status; ?>">
            <?php echo $status == "sucesso" ? "✓" : "!"; ?>
        </div>

        <div class="titulo">
            <h2><?php echo $titulo; ?></h2>
            <p><?php echo $mensagem; ?></p>
        </div>

        <?php if($status == "sucesso"){ ?>
            <div class="resumo">
                <p><strong>Produto:</strong> <?php echo $nome; ?></p>
                <p><strong>Código:</strong> <?php echo $codigo; ?></p>
                <p><strong>Estoque:</strong> <?php echo $estoque; ?></p>
                <p><strong>Localização:</strong> <?php echo $corredor . "-" . $prateleira . "-N" . $nivel; ?></p>
            </div>
        <?php } ?>

        <div class="acoes">
            <a href="cadastrar_produto.php" class="btn">Cadastrar Outro Produto</a>
            <a href="menu.php" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </div>

</div>
<script src="assets/tema.js"></script>
</body>

</html>