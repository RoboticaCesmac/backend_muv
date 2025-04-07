<?php

namespace Tests\Unit\User;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Database\Factories\UserFactory;

class UserWebTest extends TestCase
{
    use DatabaseTransactions;

    private string $url;

    protected function setUp(): void
    {
        parent::setUp();

        $this->url = "http://localhost:8000/api/v1/web/user/";
    }

    public function test_index(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->jsonAsUser('GET', $this->url, [], $user);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'user_name',
                    'email',
                    'is_admin',
                    'is_first_login',
                    'total_km',
                    'total_points',
                    'date_of_birth',
                    'gender',
                    'created_at',
                    'updated_at',
                ],
            ],
        ]);
    }

    public function test_index_not_list_super_admin(): void
    {
        $user = UserFactory::new()->stateIsAdmin(true)->create();

        $response = $this->jsonAsUser('GET', $this->url, [], $user);

        $response->assertStatus(200);
        
        $response->assertJsonMissing(['email' => 'ADMIN']);
    }   

    public function test_show(): void
    {
        $user = UserFactory::new()->create();

        $response = $this->jsonAsUser('GET', $this->url . $user->id, [], $user);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'id',
            'user_name',
            'email',
            'is_admin',
            'is_first_login',
            'total_km',
            'total_points',
            'date_of_birth',
            'gender',
            'created_at',
            'updated_at',
        ]);
    }

    public function test_destroy_with_not_admin_user(): void
    {
        $user = UserFactory::new()->create();
        
        $response = $this->jsonAsUser('DELETE', $this->url . $user->id, [], $user);

        $response->assertStatus(403);

        $response->assertJson([
            'message' => 'Usuário não é administrador',
        ]);
    }

    public function test_destroy_with_admin_user(): void
    {
        $user = UserFactory::new()->stateIsAdmin(true)->create();

        $response = $this->jsonAsUser('DELETE', $this->url . $user->id, [], $user);

        $response->assertStatus(200);

        $response->assertJson([
            'message' => 'Usuário deletado com sucesso',
        ]);
    }

    public function test_destroy_with_not_found_user(): void
    {
        $user = UserFactory::new()->stateIsAdmin(true)->create();

        $response = $this->jsonAsUser('DELETE', $this->url . '1234567890', [], $user);

        $response->assertStatus(404);

        $response->assertJson([
            'message' => 'Usuário não encontrado',
        ]);
    }
}
