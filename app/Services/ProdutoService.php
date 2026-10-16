<?php

namespace App\Services;

use App\Models\Produto;
use InvalidArgumentException;

class ProdutoService
{
    public function create(array $dados): Produto
    {
        $this->validarPreco($dados['preco'] ?? null);

        return Produto::create($dados);
    }

    public function update(Produto $produto, array $dados): Produto
    {
        if (array_key_exists('preco', $dados)) {
            $this->validarPreco($dados['preco']);
        }

        $produto->update($dados);

        return $produto->fresh();
    }

    private function validarPreco(mixed $preco): void
    {
        if (!is_numeric($preco) || $preco <= 0) {
            throw new InvalidArgumentException('O preço nao pode ser zero.');
        }
    }
}