<?php

/**
 * Model responsável pelo histórico de entradas e saídas de estoque.
 */
class Movimentacao
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function registrar(string $codigoBarras, string $produto, int $quantidade, string $localizacao, string $tipo): bool
    {
        $sql = 'INSERT INTO movimentacoes (codigo_barras, produto, quantidade, localizacao, tipo, data_movimentacao)
                VALUES (:codigo_barras, :produto, :quantidade, :localizacao, :tipo, NOW())';

        $stmt = $this->pdo->prepare($sql);

        return $stmt->execute([
            'codigo_barras' => $codigoBarras,
            'produto' => $produto,
            'quantidade' => $quantidade,
            'localizacao' => $localizacao,
            'tipo' => $tipo,
        ]);
    }

    public function todas(): array
    {
        return $this->pdo->query('SELECT * FROM movimentacoes ORDER BY data_movimentacao DESC')->fetchAll();
    }

    public function ultimas(int $limite = 5): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM movimentacoes ORDER BY data_movimentacao DESC LIMIT :limite');
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function contarPorTipo(string $tipo): int
    {
        $stmt = $this->pdo->prepare('SELECT COUNT(*) FROM movimentacoes WHERE tipo = :tipo');
        $stmt->execute(['tipo' => $tipo]);
        return (int) $stmt->fetchColumn();
    }
}
