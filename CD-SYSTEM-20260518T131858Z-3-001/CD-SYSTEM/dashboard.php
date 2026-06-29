<?php
include "conectar.php";

$totalProdutos = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM produtos"));
$totalEstoque = mysqli_fetch_assoc(mysqli_query($conn, "SELECT SUM(estoque) AS total FROM produtos"));
$entradas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM movimentacoes WHERE tipo = 'ENTRADA'"));
$saidas = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM movimentacoes WHERE tipo = 'SAIDA'"));
$estoqueBaixo = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM produtos WHERE estoque <= 5"));

$produtos = mysqli_query($conn, "SELECT * FROM produtos ORDER BY nome ASC LIMIT 6");
$ultimas = mysqli_query($conn, "SELECT * FROM movimentacoes ORDER BY data_movimentacao DESC LIMIT 5");
?>

<!DOCTYPE html>
<head>
<meta charset="UTF-8">
<title>Dashboard - FindWare</title>
<link rel="stylesheet" href="assets/style.css">
<script src="assets/tema.js" defer></script>
<link rel="stylesheet" href="assets/css/style.css">

<style>
.cards-dashboard{
    display:grid;
    grid-template-columns:repeat(5,1fr);
    gap:16px;
    margin-bottom:24px;
}

.card + .card{
    margin-top:24px;
}

.acoes-tabela{
    display:flex;
    gap:8px;
    justify-content:center;
}

@media(max-width:1100px){
    .cards-dashboard{
        grid-template-columns:repeat(2,1fr);
    }
}

@media(max-width:650px){
    .cards-dashboard{
        grid-template-columns:1fr;
    }
}
</style>
</head>

<body class="tema-escuro">

<?php include "includes/topo.php"; ?>

<div class="container">

    <div class="titulo">
        <h2>Dashboard do Centro de Distribuição</h2>
        <p>Visão geral dos produtos, estoque e movimentações do FindWare.</p>
    </div>

    <div class="cards-dashboard">

        <div class="card-indicador">
            <h3>Total de Produtos</h3>
            <div class="numero"><?php echo $totalProdutos['total']; ?></div>
        </div>

        <div class="card-indicador">
            <h3>Total em Estoque</h3>
            <div class="numero"><?php echo $totalEstoque['total'] ?? 0; ?></div>
        </div>

        <div class="card-indicador">
            <h3>Entradas</h3>
            <div class="numero entrada"><?php echo $entradas['total']; ?></div>
        </div>

        <div class="card-indicador">
            <h3>Saídas</h3>
            <div class="numero saida"><?php echo $saidas['total']; ?></div>
        </div>

        <div class="card-indicador">
            <h3>Estoque Baixo</h3>
            <div class="numero alerta"><?php echo $estoqueBaixo['total']; ?></div>
        </div>

    </div>

    <div class="card">

        <div class="titulo">
            <h2>Produtos cadastrados</h2>
    
        </div>

        <div class="tabela-container">
            <table>
                <tr>
                    <th>Produto</th>
                    <th>Código</th>
                    <th>QR Code</th>
                    <th>Localização</th>
                    <th>Estoque</th>
                    <th>Ações</th>
                </tr>

                <?php if(mysqli_num_rows($produtos) > 0): ?>
                    <?php while($produto = mysqli_fetch_assoc($produtos)): ?>

                    <tr>
                        <td><?php echo $produto['nome']; ?></td>
                        <td><?php echo $produto['codigo']; ?></td>
                        <td><?php echo $produto['codigo_barras']; ?></td>

                        <td>
                            <?php
                            echo strtoupper($produto['corredor']) .
                                 "-" .
                                 str_pad($produto['prateleira'], 2, "0", STR_PAD_LEFT) .
                                 "-N" .
                                 $produto['nivel'];
                            ?>
                        </td>

                        <td>
                            <span class="badge-estoque <?php echo $produto['estoque'] <= 5 ? 'estoque-baixo' : 'estoque-ok'; ?>">
                                <?php echo $produto['estoque']; ?>
                            </span>
                        </td>

                        <td>
                            <div class="acoes-tabela">
                                <a href="localizar.php?busca=<?php echo $produto['codigo_barras']; ?>" class="btn">Localizar</a>
                                <a href="mapa.php?busca=<?php echo $produto['codigo_barras']; ?>" class="btn btn-secundario">Mapa</a>
                            </div>
                        </td>
                    </tr>

                    <?php endwhile; ?>
                <?php else: ?>

                    <tr>
                        <td colspan="6" class="vazio">Nenhum produto cadastrado.</td>
                    </tr>

                <?php endif; ?>
            </table>
        </div>

    </div>

    <div class="card">

        <div class="titulo">
            <h2>Últimas Movimentações</h2>
            <p>Resumo das últimas entradas e saídas registradas.</p>
        </div>

        <div class="tabela-container">
            <table>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Tipo</th>
                    <th>Localização / Motivo</th>
                    <th>Data</th>
                </tr>

                <?php if(mysqli_num_rows($ultimas) > 0): ?>
                    <?php while($mov = mysqli_fetch_assoc($ultimas)): ?>

                    <tr>
                        <td><?php echo $mov['produto']; ?></td>
                        <td><?php echo $mov['quantidade']; ?></td>
                        <td>
                            <span class="badge <?php echo strtolower($mov['tipo']); ?>">
                                <?php echo $mov['tipo']; ?>
                            </span>
                        </td>
                        <td><?php echo $mov['localizacao']; ?></td>
                        <td><?php echo $mov['data_movimentacao']; ?></td>
                    </tr>

                    <?php endwhile; ?>
                <?php else: ?>

                    <tr>
                        <td colspan="5" class="vazio">Nenhuma movimentação registrada.</td>
                    </tr>

                <?php endif; ?>
            </table>
        </div>

        <div class="acoes">
            <a href="menu.php" class="btn btn-secundario">Voltar ao Menu</a>
            <a href="movimentacoes.php" class="btn">Ver Histórico Completo</a>
        </div>

    </div>

</div>

</body>
</html>