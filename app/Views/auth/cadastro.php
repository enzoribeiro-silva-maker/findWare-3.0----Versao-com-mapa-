<div class="cabecalho">
    <h2>Criar conta</h2>
    <p>Cadastre-se para acessar o sistema FindWare.</p>
</div>

<form action="<?= url('cadastro') ?>" method="POST">

    <label>Nome</label>
    <input type="text" name="nome" placeholder="Digite seu nome" required autofocus>

    <label>E-mail</label>
    <input type="email" name="email" placeholder="Digite seu e-mail" required>

    <label>Senha</label>
    <input type="password" name="senha" placeholder="Digite sua senha" required>

    <button type="submit">Cadastrar Usuário</button>

</form>

<div class="auth-links">
    Já tem uma conta? <a href="<?= url('login') ?>">Fazer login</a>
</div>
