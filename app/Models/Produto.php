<?php

/**
 * Model responsável por toda a regra de acesso a dados da tabela `produtos`.
 */
class Produto
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function todos(?int $limite = null): array
    {
        $sql = 'SELECT * FROM produtos ORDER BY nome ASC';

        if ($limite) {
            $sql .= ' LIMIT ' . intval($limite);
        }

        return $this->pdo->query($sql)->fetchAll();
    }

    public function buscarPorTermo(string $termo): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM produtos WHERE nome LIKE :termo OR codigo LIKE :termo OR codigo_barras LIKE :termo ORDER BY nome ASC'
        );
        $stmt->execute(['termo' => "%{$termo}%"]);
        return $stmt->fetchAll();
    }

    public function buscarPorCodigo(string $codigo): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM produtos WHERE codigo_barras = :codigo OR codigo = :codigo LIMIT 1'
        );
        $stmt->execute(['codigo' => $codigo]);
        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    public function buscarPorTermoOuCodigo(string $termo): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM produtos WHERE codigo_barras = :termo OR codigo = :termo OR nome LIKE :like LIMIT 1'
        );
        $stmt->execute(['termo' => $termo, 'like' => "%{$termo}%"]);
        $produto = $stmt->fetch();
        return $produto ?: null;
    }

    public function criar(array $dados): bool
    {
        $sql = 'INSERT INTO produtos (nome, codigo, codigo_barras, corredor, prateleira, nivel, estoque)
                VALUES (:nome, :codigo, :codigo_barras, :corredor, :prateleira, :nivel, :estoque)';

        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute($dados);
    }

    public function entradaOuCriar(string $codigo, string $nome, int $quantidade, string $corredor, string $prateleira, string $nivel): void
    {
        $existente = $this->buscarPorCodigo($codigo);

        if ($existente) {
            $stmt = $this->pdo->prepare(
                'UPDATE produtos SET estoque = estoque + :quantidade, corredor = :corredor, prateleira = :prateleira, nivel = :nivel WHERE id = :id'
            );
            $stmt->execute([
                'quantidade' => $quantidade,
                'corredor' => $corredor,
                'prateleira' => $prateleira,
                'nivel' => $nivel,
                'id' => $existente['id'],
            ]);
            return;
        }

        $this->criar([
            'nome' => $nome !== '' ? $nome : $codigo,
            'codigo' => $codigo,
            'codigo_barras' => $codigo,
            'corredor' => $corredor,
            'prateleira' => $prateleira,
            'nivel' => $nivel,
            'estoque' => $quantidade,
        ]);
    }

    public function darSaida(string $codigo, int $quantidade): array
    {
        $produto = $this->buscarPorCodigo($codigo);

        if (!$produto) {
            return ['ok' => false, 'motivo' => 'nao_encontrado', 'erro' => 'Nenhum produto foi encontrado com o código informado.'];
        }

        if ((int) $produto['estoque'] < $quantidade) {
            return [
                'ok' => false,
                'motivo' => 'estoque_insuficiente',
                'erro' => "O estoque atual é de {$produto['estoque']} unidade(s), menor que a quantidade solicitada.",
                'produto' => $produto,
            ];
        }

        $stmt = $this->pdo->prepare('UPDATE produtos SET estoque = estoque - :quantidade WHERE id = :id');
        $stmt->execute(['quantidade' => $quantidade, 'id' => $produto['id']]);

        return ['ok' => true, 'produto' => $produto];
    }

    public function atualizarLocalizacao(string $codigo, string $corredor, string $prateleira, string $nivel): bool
    {
        $stmt = $this->pdo->prepare(
            'UPDATE produtos SET corredor = :corredor, prateleira = :prateleira, nivel = :nivel WHERE codigo_barras = :codigo OR codigo = :codigo'
        );
        return $stmt->execute(compact('corredor', 'prateleira', 'nivel', 'codigo'));
    }

    public function excluir(int $id): bool
    {
        $stmt = $this->pdo->prepare('DELETE FROM produtos WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    public function contar(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM produtos')->fetchColumn();
    }

    public function somarEstoque(): int
    {
        return (int) $this->pdo->query('SELECT COALESCE(SUM(estoque), 0) FROM produtos')->fetchColumn();
    }

    public function contarEstoqueBaixo(int $limite = 5): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM produtos WHERE estoque <= :limite');
        $stmt->execute(['limite' => $limite]);
        return (int) $stmt->fetchColumn();
    }
}
