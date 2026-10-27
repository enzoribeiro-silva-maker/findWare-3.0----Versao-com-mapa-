<?php
require_once __DIR__ . '/../Models/Produto.php';
require_once __DIR__ . '/../Models/Movimentacao.php';

class DashboardController extends Controller
{
    public function menu(): void
    {
        $produtoModel = new Produto();
        $movimentacaoModel = new Movimentacao();

        $this->view('dashboard/menu', $this->indicadores($produtoModel, $movimentacaoModel));
    }

    public function dashboard(): void
    {
        $produtoModel = new Produto();
        $movimentacaoModel = new Movimentacao();

        $dados = $this->indicadores($produtoModel, $movimentacaoModel);
        $dados['produtos'] = $produtoModel->todos(6);
        $dados['ultimasMovimentacoes'] = $movimentacaoModel->ultimas(5);

        $this->view('dashboard/dashboard', $dados);
    }

    private function indicadores(Produto $produtoModel, Movimentacao $movimentacaoModel): array
    {
        return [
            'totalProdutos' => $produtoModel->contar(),
            'totalEstoque' => $produtoModel->somarEstoque(),
            'entradas' => $movimentacaoModel->contarPorTipo('ENTRADA'),
            'saidas' => $movimentacaoModel->contarPorTipo('SAIDA'),
            'estoqueBaixo' => $produtoModel->contarEstoqueBaixo(),
        ];
    }
}
