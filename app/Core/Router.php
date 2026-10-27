<?php

/**
 * Router bem simples: mapeia "método HTTP + caminho" para uma controller/ação
 * (ou uma função anônima). Sem dependências externas — só PHP puro.
 */
class Router
{
    private array $rotas = [];

    public function get(string $caminho, $acao): void
    {
        $this->rotas['GET'][$this->normalizar($caminho)] = $acao;
    }

    public function post(string $caminho, $acao): void
    {
        $this->rotas['POST'][$this->normalizar($caminho)] = $acao;
    }

    private function normalizar(string $caminho): string
    {
        $caminho = trim($caminho, '/');
        return $caminho === '' ? '/' : $caminho;
    }

    public function despachar(): void
    {
        $metodo = $_SERVER['REQUEST_METHOD'];
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';

        $basePath = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
        if ($basePath !== '/' && strpos($uri, $basePath) === 0) {
            $uri = substr($uri, strlen($basePath));
        }

        $uri = $this->normalizar($uri);

        $acao = $this->rotas[$metodo][$uri] ?? null;

        if (!$acao) {
            http_response_code(404);
            require __DIR__ . '/../Views/erro/404.php';
            return;
        }

        if (is_array($acao)) {
            [$classe, $metodoAcao] = $acao;
            (new $classe())->$metodoAcao();
            return;
        }

        // Função anônima (usada só para redirecionamentos simples)
        $acao();
    }
}
