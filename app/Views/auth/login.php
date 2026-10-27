<div class="cabecalho">
    <h2>Bem-vindo de volta</h2>
    <p>Entre com suas credenciais para acessar o FindWare.</p>
</div>

<?php if (!empty($erro)): ?>
    <div class="alerta-erro-login">
        O e-mail ou senha informado não corresponde a nenhum usuário cadastrado.
    </div>
<?php endif; ?>

<form action="<?= url('login') ?>" method="POST">

    <label>Usuário ou E-mail</label>
    <input type="text" name="email" placeholder="Digite seu usuário ou e-mail" required autofocus>

    <label>Senha</label>
    <input type="password" name="senha" placeholder="Digite sua senha" required>

    <button type="submit">Entrar no Sistema</button>

</form>

<div class="auth-links">
    Ainda não tem uma conta? <a href="<?= url('cadastro') ?>">Criar novo usuário</a>
</div>
