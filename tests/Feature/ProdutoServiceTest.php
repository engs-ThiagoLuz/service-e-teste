<?php

namespace Tests\Feature;

use App\Models\Produto;
use App\Services\ProdutoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

class ProdutoServiceTest extends TestCase
{
    use RefreshDatabase;

    private ProdutoService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ProdutoService();
    }

    //1) Não é criado produto com valor fora da regra
    public function test_nao_cria_produto_com_preco_invalido(): void
    {
        $this->expectException(InvalidArgumentException::class);

        try {
            $this->service->create(['nome' => 'Produto X', 'preco' => -10]);
        } finally {
            $this->assertDatabaseCount('produtos', 0);
        }
    }

    // 2) Teste 1: Atualização com dados válidos
    public function test_atualiza_produto_com_dados_validos(): void
    {
        $produto = Produto::factory()->create(['nome' => 'Antigo', 'preco' => 50]);

        $atualizado = $this->service->update($produto, [
            'nome'  => 'Novo',
            'preco' => 99.90,
        ]);

        $this->assertEquals('Novo', $atualizado->nome);
        $this->assertEquals(99.90, $atualizado->preco);
        $this->assertDatabaseHas('produtos', [
            'id'    => $produto->id,
            'nome'  => 'Novo',
            'preco' => 99.90,
        ]);
    }

    // 2) Teste 2: Não aceita preço inválido e não é alterado no banco
    public function test_nao_atualiza_produto_com_preco_invalido(): void
    {
        $produto = Produto::factory()->create(['nome' => 'Original', 'preco' => 50]);

        try {
            $this->service->update($produto, ['preco' => 0]);
            $this->fail('Deveria ter lançado InvalidArgumentException.');
        } catch (InvalidArgumentException $e) {
            $this->assertEquals('O preço nao pode ser zero.', $e->getMessage());
        }

        $this->assertDatabaseHas('produtos', [
            'id'    => $produto->id,
            'preco' => 50,
        ]);
    }
}