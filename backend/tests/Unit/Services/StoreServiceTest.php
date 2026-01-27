<?php

use Tests\TestCase;
use Mockery;
use App\Models\Store;
use App\Models\User;
use App\Services\StoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;

class StoreServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_can_list_all_stores(): void
    {
        // Criar instâncias no banco de teste
        Store::factory()->create(['name' => 'Loja 1']);
        Store::factory()->create(['name' => 'Loja 2']);

        // Chamar o serviço
        $stores = StoreService::index();

        // Verificar se retornou os dois registros
        $this->assertCount(2, $stores);
        $this->assertEquals('Loja 1', $stores[0]->name);
    }

    /** @test */
    public function test_it_can_create_a_store(): void
    {
        $user = User::factory()->create(); // Criar usuário de teste

        $data = [
            'name' => 'Nova Loja',
            'description' => 'Descrição da Loja',
            'image' => 'imagem.jpg',
            'public_id' => '12345',
            'user_id' => $user->id,
            'active' => true,
            'whatsapp' => '84 986460846',
        ];

        $service = new StoreService();
        $store = $service->store($data);

        // Verifica se a loja foi criada no banco de dados
        $this->assertDatabaseHas('stores', ['name' => 'Nova Loja']);

        // Verifica se o retorno do serviço contém os dados corretos
        $this->assertInstanceOf(Store::class, $store);
        $this->assertEquals('Nova Loja', $store->name);
    }

    /** @test */
    public function test_it_can_update_a_store()
    {
        // Criando uma loja no banco de testes
        $store = Store::factory()->create(['name' => 'Loja Antiga']);

        $service = new StoreService();
        $updatedStore = $service->update(['name' => 'Loja Atualizada'], $store);

        // Verifica se o nome foi atualizado corretamente
        $this->assertEquals('Loja Atualizada', $updatedStore->name);

        // Verifica se o banco de dados contém a atualização
        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => 'Loja Atualizada',
        ]);
    }

    /** @test */
    public function test_it_can_delete_a_store()
    {
        // Criando uma loja no banco de testes
        $store = Store::factory()->create();

        $service = new StoreService();
        $service->destroy($store);

        // Verifica que a loja sofreu soft-delete do banco de dados
        $this->assertDatabaseHas('stores', ['deleted_at' => $store->deleted_at]);
    }

    /** @test */
    public function test_it_can_toggle_store_active_status()
    {
        // Criando uma loja no banco de testes com active = true
        $store = Store::factory()->create(['active' => true]);

        $service = new StoreService();
        $service->changeActive($store);

        // Atualizando a instância do modelo após a mudança no banco de dados
        $store->refresh();

        // Verificando que o status foi alterado corretamente
        $this->assertFalse($store->active);
    }
    
    // protected function tearDown(): void
    // {
    //     Mockery::close();
    //     parent::tearDown();
    // }
}
