<?php

/**
 * Controller base: toda controller do sistema estende esta classe.
 * Cuida da proteção de rotas (login obrigatório) e da renderização das views.
 */
class Controller
{
    public function __construct(bool $protegido = true)
    {
        if ($protegido) {
            exigirLogin();
        }
    }

    protected function view(string $view, array $dados = [], ?string $layout = 'app'): void
    {
        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (!file_exists($viewPath)) {
            die("View não encontrada: {$view}");
        }

        if ($layout) {
            // A view é renderizada "dentro" do layout através da função $conteudo()
            $conteudo = function () use ($viewPath, $dados) {
                extract($dados);
                require $viewPath;
            };

            extract($dados);
            require __DIR__ . '/../Views/layout/' . $layout . '.php';
        } else {
            extract($dados);
            require $viewPath;
        }
    }

    protected function redirect(string $rota): void
    {
        redirecionar($rota);
    }

    protected function json(array $dados): void
    {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($dados);
        exit;
    }
}
