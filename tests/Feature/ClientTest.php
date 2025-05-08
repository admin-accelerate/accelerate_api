<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        Sanctum::actingAs($admin);

        $response = $this->postJson('/api/v1/clients', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'phone' => '1234567890',
            'address' => '123 St Michel',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'client' => ['id', 'name', 'email', 'phone', 'address'],
                     'message'
                 ])
                 ->assertJson(['message' => 'Client créé avec succès !']);
    }

    public function test_unauthenticated_user_cannot_create_client()
    {
        $response = $this->postJson('/api/v1/clients', [
            'name' => 'Odilon FANOU',
            'email' => 'odilon@fanou.com',
        ]);

        $response->assertStatus(401);
    }

    /*public function test_admin_can_update_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'address' => '456 St Michel',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'client' => ['id', 'name', 'email', 'phone', 'address'],
                     'message'
                 ])
                 ->assertJson([
                     'message' => 'Client mise à jour avec succès !',
                     'client' => [
                         'name' => 'Jane Doe',
                         'email' => 'jane@example.com',
                         'phone' => '0987654321',
                         'address' => '456 St Michel',
                     ]
                 ]);
    }*/

    public function test_admin_can_update_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        Sanctum::actingAs($admin);

        $response = $this->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'address' => '456 St Michel',
        ]);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'client' => ['id', 'name', 'email', 'phone', 'address'],
                     'message'
                 ])
                 ->assertJson([
                     'message' => 'Client mise à jour avec succès !',
                     'client' => [
                         'name' => 'Jane Doe',
                         'email' => 'jane@example.com',
                         'phone' => '0987654321',
                         'address' => '456 St Michel',
                     ]
                 ]);
    }

    /**
     * Test that admin can delete a client
     *
     * @return void
     */
    public function test_admin_can_delete_client()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Client supprimé avec succès']);

        $this->assertDatabaseMissing('clients', ['id' => $client->id]);
    }

    /**
     * Test that unauthenticated user cannot delete a client
     *
     * @return void
     */
    public function test_unauthenticated_user_cannot_delete_client()
    {
        $client = Client::factory()->create();

        $response = $this->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(401);
    }


     /**
     * Test non-admin user cannot update client 
     *
     * @return void
     */
    /*public function test_non_admin_cannot_update_client()
    {
        $user = User::factory()->create(['role' => 'user']);
        $client = Client::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->putJson("/api/v1/clients/{$client->id}", [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'phone' => '0987654321',
            'address' => '456 St Michel',
        ]);

        $response->assertStatus(403)
                 ->assertJson(['message' => "Vous n'avez l'autorisation d'effectuer cette action"]);
    }*/
    /**
     * Test that non-admin user cannot delete a client
     *
     * @return void
     */
    /*public function test_non_admin_cannot_delete_client()
    {
        $user = User::factory()->create(['role' => 'user']);
        $client = Client::factory()->create();
        Sanctum::actingAs($user);

        $response = $this->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(403)
                 ->assertJson(['message' => "Vous n'avez l'autorisation d'effectuer cette action"]);
    }*/

    /*public function test_cannot_delete_client_with_invoices()
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $client = Client::factory()->create();
        $invoice = Invoice::factory()->create(['client_id' => $client->id]);
        Sanctum::actingAs($admin);

        $response = $this->deleteJson("/api/v1/clients/{$client->id}");

        $response->assertStatus(422)
                ->assertJson(['message' => 'Impossible de supprimer un client avec des factures associées']);
    }*/


}