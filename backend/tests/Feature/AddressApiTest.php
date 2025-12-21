<?php

namespace Tests\Feature\API;

use App\Models\Address;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressApiTest extends TestCase
{
    use RefreshDatabase;

    private function addressPayload(array $override = []): array
    {
        return array_merge([
            'cep' => '59218000',
            'state' => 'RN',
            'city' => 'Passa e Fica',
            'district' => 'Zona Rural',
            'street' => 'Cipoal',
            'number' => '73',
            'whatsapp' => '84986460846',
            'latitude' => '-6.4343',
            'longitude' => '-35.6432',
        ], $override);
    }

    public function test_can_create_address_for_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/api/address', $this->addressPayload([
            'user_id' => $user->id,
        ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('addresses', [
            'user_id' => $user->id,
            'cep' => '59218000',
        ]);
    }

    public function test_can_create_address_for_store()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->postJson('/api/address', $this->addressPayload([
            'store_id' => $store->id,
        ]));

        $response->assertStatus(201);

        $this->assertDatabaseHas('addresses', [
            'store_id' => $store->id,
        ]);
    }

    public function test_cannot_create_second_address_for_same_user()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $this->actingAs($user);

        $response = $this->postJson('/api/address', $this->addressPayload([
            'user_id' => $user->id,
        ]));

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthorized. An address for this user or store already exists.'
            ]);
    }

    public function test_cannot_create_address_without_user_or_store()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/api/address', $this->addressPayload());

        $response->assertStatus(401);
    }

    public function test_user_can_update_own_address()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $address = Address::factory()->create([
            'user_id' => $user->id,
            'city' => 'Cidade Antiga',
        ]);

        $response = $this->putJson(
            "/api/address/{$address->id}",
            $this->addressPayload([
                'user_id' => $user->id,
                'city' => 'Nova Cidade',
            ])
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'city' => 'Nova Cidade',
        ]);
    }

    public function test_cannot_update_address_of_another_user()
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($otherUser);

        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->putJson(
            "/api/address/{$address->id}",
            $this->addressPayload([
                'user_id' => $otherUser->id,
            ])
        );

        $response
            ->assertStatus(401)
            ->assertJson([
                'message' => 'The address is not authorized for this action.'
            ]);
    }

    public function test_store_owner_can_update_store_address()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $address = Address::factory()->create([
            'store_id' => $store->id,
            'city' => 'Antiga',
        ]);

        $response = $this->putJson(
            "/api/address/{$address->id}",
            $this->addressPayload([
                'store_id' => $store->id,
                'city' => 'Atualizada',
            ])
        );

        $response->assertStatus(200);

        $this->assertDatabaseHas('addresses', [
            'id' => $address->id,
            'city' => 'Atualizada',
        ]);
    }

    public function test_cannot_change_address_owner_from_user_to_store()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        $address = Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->putJson(
            "/api/address/{$address->id}",
            $this->addressPayload([
                'store_id' => $store->id,
            ])
        );

        $response->assertStatus(401);
    }

    public function test_can_get_user_address()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        Address::factory()->create([
            'user_id' => $user->id,
        ]);

        $response = $this->getJson("/api/address/user/{$user->id}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'address' => [
                    '*' => ['id', 'cep', 'city']
                ]
            ]);
    }

    public function test_can_get_store_address()
    {
        $user = User::factory()->create();
        $store = Store::factory()->create(['owner_id' => $user->id]);

        $this->actingAs($user);

        Address::factory()->create([
            'store_id' => $store->id,
        ]);

        $response = $this->getJson("/api/address/store/{$store->id}");

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'address' => [
                    '*' => ['id', 'cep', 'city']
                ]
            ]);
    }
}
