
<?php

class EnderecoArmazenagem
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::getConnection();
    }

    public function todos(): array
    {
        $sql = "
            SELECT
                e.id,
                e.codigo_endereco,
                e.corredor,
                e.estante,
                e.nivel,
                e.status,
                e.produto_id,
                p.nome AS produto_nome,
                p.codigo AS produto_codigo,
                p.estoque AS produto_estoque
            FROM public.enderecos_armazenagem e
            LEFT JOIN public.produtos p ON p.id = e.produto_id
            ORDER BY e.corredor, e.estante, e.nivel
        ";

        return $this->pdo->query($sql)->fetchAll();
    }
    
public function vagas(): array
{
    $sql = "
        SELECT
            id,
            codigo_endereco,
            corredor,
            estante,
            nivel
        FROM public.enderecos_armazenagem
        WHERE produto_id IS NULL
          AND status = 'vago'
        ORDER BY corredor, estante, nivel
    ";

    return $this->pdo->query($sql)->fetchAll();
}

public function ocupar(int $enderecoId, int $produtoId): bool
{
    $sql = "
        UPDATE public.enderecos_armazenagem
        SET produto_id = :produto_id
        WHERE id = :endereco_id
          AND produto_id IS NULL
          AND status = 'vago'
    ";

    $stmt = $this->pdo->prepare($sql);

    $stmt->execute([
        'produto_id' => $produtoId,
        'endereco_id' => $enderecoId,
    ]);

    return $stmt->rowCount() === 1;
}


}
