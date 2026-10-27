<?php
/**
 * Funções auxiliares usadas nas Views e Controllers.
 */

function url(string $caminho = ''): string
{
    $caminho = ltrim($caminho, '/');
    return BASE_URL . '/' . $caminho;
}

function asset(string $caminho): string
{
    return url('assets/' . ltrim($caminho, '/'));
}

function e($valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

function usuarioLogado()
{
    return $_SESSION['usuario'] ?? null;
}

function exigirLogin(): void
{
    if (!usuarioLogado()) {
        redirecionar('login');
    }
}

function redirecionar(string $rota): void
{
    header('Location: ' . url($rota));
    exit;
}
