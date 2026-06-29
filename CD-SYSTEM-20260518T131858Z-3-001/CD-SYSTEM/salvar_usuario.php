<?php

include "conectar.php";

$nome = $_POST['nome'];
$email = $_POST['email'];
$senha = $_POST['senha'];

$sql = "INSERT INTO usuarios(nome, email, senha)
VALUES('$nome', '$email', '$senha')";

if(mysqli_query($conn, $sql)) {
    $status = "sucesso";
    $titulo = "Usuário cadastrado com sucesso!";
    $mensagem = "Agora você já pode acessar o sistema FindWare usando seu e-mail e senha.";
} else {
    $status = "erro";
    $titulo = "Erro ao cadastrar usuário";
    $mensagem = mysqli_error($conn);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<title>Cadastro de Usuário - FindWare</title>
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
    max-width: 540px;
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

    <span>Cadastro de Usuário</span>
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
                <p><strong>Nome:</strong> <?php echo $nome; ?></p>
                <p><strong>E-mail:</strong> <?php echo $email; ?></p>
            </div>
        <?php } ?>

        <div class="acoes">
            <a href="login.php" class="btn">Ir para Login</a>
            <a href="cadastro.php" class="btn btn-secundario">Cadastrar Outro</a>
        </div>

    </div>

</div>
<script src="assets/tema.js"></script>
</body>

</html>