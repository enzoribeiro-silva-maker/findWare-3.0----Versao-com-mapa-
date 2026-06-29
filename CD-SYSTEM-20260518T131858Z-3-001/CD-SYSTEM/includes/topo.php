<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$usuarioLogado = $_SESSION['usuario'] ?? 'Administrador';
?>

<header class="topbar">
    <div>
        <h1>FindWare</h1>
        <p>Sistema Inteligente de Localização de Produtos</p>
    </div>

    <div class="top-actions">
        <span class="user-badge">
            Usuário: <strong><?php echo $usuarioLogado; ?></strong>
        </span>

        <button class="hamburger" type="button" onclick="toggleMenu()">☰</button>
    </div>
</header>

<nav id="sideMenu" class="side-menu">
    <button class="close-menu" type="button" onclick="toggleMenu()">×</button>

    <h2>Menu</h2>

    <a href="menu.php">Painel Principal</a>
    <a href="dashboard.php">Dashboard</a>
    <a href="scanner.php">Scanner</a>
    <a href="localizar.php">Localizar Produto</a>
    <a href="procurar_produto.php">Produto Perdido</a>
    <a href="movimentacoes.php">Movimentações</a>
    <a href="logout.php" class="logout">Sair</a>
</nav>

<div id="overlay" class="overlay" onclick="toggleMenu()"></div>

<script>
function toggleMenu(){
    document.getElementById("sideMenu").classList.toggle("active");
    document.getElementById("overlay").classList.toggle("active");
}
</script>