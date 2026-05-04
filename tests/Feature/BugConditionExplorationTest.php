<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class BugConditionExplorationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $installedFile = storage_path('installed');
        if (File::exists($installedFile)) {
            File::delete($installedFile);
        }
    }

    public function test_installer_route_loads_complete_interface(): void
    {
        $viewPath = resource_path('views/instalador/index.blade.php');
        $this->assertTrue(File::exists($viewPath), 'A view do instalador deve existir.');

        $response = $this->get('/instalar');

        $response->assertStatus(200);
        $response->assertViewIs('instalador.index');
        $response->assertSee('Instalador FinanceiroSaaS', false);
        $response->assertSee('Banco de dados MariaDB/MySQL', false);
        $response->assertSee('Finalizar instalação', false);
    }

    public function test_root_route_redirects_to_installer_when_not_installed(): void
    {
        $this->assertFalse(File::exists(storage_path('installed')));

        $response = $this->get('/');

        $response->assertRedirect('/instalar');
    }

    public function test_database_configuration_supports_sqlite_testing_and_mysql_production(): void
    {
        $this->assertSame('sqlite', config('database.default'));
        $this->assertSame('mysql', config('database.connections.mysql.driver'));

        $envExample = File::get(base_path('.env.example'));
        $this->assertStringContainsString('DB_CONNECTION=mysql', $envExample);
    }

    public function test_installer_requirements_check_php_and_mysql_extensions(): void
    {
        $response = $this->get('/instalar/requisitos');

        $response->assertStatus(200);
        $response->assertJson(['sucesso' => true]);
        $response->assertJsonFragment(['nome' => 'PHP >= 8.4']);
        $response->assertJsonFragment(['nome' => 'ext-pdo_mysql']);
    }

    public function test_installer_permissions_endpoint_is_available(): void
    {
        $response = $this->get('/instalar/permissoes');

        $response->assertStatus(200);
        $response->assertJson(['sucesso' => true]);
        $response->assertJsonStructure(['sucesso', 'pastas', 'tudo_ok']);
    }
}
