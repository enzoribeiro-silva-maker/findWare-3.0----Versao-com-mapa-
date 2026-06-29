<?php
include "conectar.php";

$codigo_barras = $_POST['codigo_barras'];
$produto = $_POST['produto'];
$quantidade = intval($_POST['quantidade']);
$motivo = $_POST['motivo'];

$status = "";
$titulo = "";
$mensagem = "";
$estoqueAtual = 0;

$verifica = mysqli_query($conn, "SELECT * FROM produtos 
WHERE codigo_barras = '$codigo_barras'
OR codigo = '$codigo_barras'");

if(mysqli_num_rows($verifica) == 0){

    $status = "erro";
    $titulo = "Produto não encontrado";
    $mensagem = "Nenhum produto foi encontrado com o código informado.";

}else{

    $dado = mysqli_fetch_assoc($verifica);
    $estoqueAtual = intval($dado['estoque']);

    if($estoqueAtual < $quantidade){

        $status = "erro";
        $titulo = "Estoque insuficiente";
        $mensagem = "O estoque atual é de $estoqueAtual unidade(s), menor que a quantidade solicitada.";

    }else{

        $sqlProduto = "UPDATE produtos
                       SET estoque = estoque - $quantidade
                       WHERE codigo_barras = '$codigo_barras'
                       OR codigo = '$codigo_barras'";

        mysqli_query($conn, $sqlProduto);

        $sqlMov = "INSERT INTO movimentacoes
        (codigo_barras, produto, quantidade, localizacao, tipo, data_movimentacao)
        VALUES
        ('$codigo_barras', '$produto', $quantidade, '$motivo', 'SAIDA', NOW())";

        if(mysqli_query($conn, $sqlMov)){
            $status = "sucesso";
            $titulo = "Saída registrada com sucesso!";
            $mensagem = "A saída do produto foi registrada no sistema.";
        }else{
            $status = "erro";
            $titulo = "Erro ao registrar saída";
            $mensagem = mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Saída de Produtos - FindWare</title>
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

    <span>Resultado da Saída</span>
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
                <p><strong>Produto:</strong> <?php echo $produto; ?></p>
                <p><strong>Código:</strong> <?php echo $codigo_barras; ?></p>
                <p><strong>Quantidade retirada:</strong> <?php echo $quantidade; ?></p>
                <p><strong>Motivo:</strong> <?php echo $motivo; ?></p>
                <p><strong>Estoque anterior:</strong> <?php echo $estoqueAtual; ?></p>
                <p><strong>Estoque atual:</strong> <?php echo $estoqueAtual - $quantidade; ?></p>
            </div>
        <?php } ?>

        <div class="acoes">
            <a href="saida_produto.php" class="btn">Nova Saída</a>
            <a href="movimentacoes.php" class="btn btn-secundario">Ver Movimentações</a>
            <a href="menu.php" class="btn btn-secundario">Voltar ao Menu</a>
        </div>

    </div>

</div>
<script src="assets/tema.js"></script>
</body>

</html>