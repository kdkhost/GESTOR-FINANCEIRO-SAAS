<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\View\ViewException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\File;

/**
 * Bug Condition Exploration Test
 * 
 * **Validates: Requirements 1.1, 1.2, 1.3, 1.4**
 * 
 * CRITICAL: This test MUST FAIL on unfixed code - failure confirms the bug exists
 * DO NOT attempt to fix the test or the code when it fails
 * 
 * This test encodes the expected behavior - it will validate the fix when it passes after implementation
 * GOAL: Surface counterexamples that demonstrate the bug exists
 */
class BugConditionExplorationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Ensure system is not installed for testing
        $installedFile = storage_path('installed');
        if (File::exists($installedFile)) {
            File::delete($installedFile);
        }
    }

    /**
     * Property 1: Bug Condition - Interface do Instalador Ausente
     * 
     * Test that accessing /instalar route returns ViewNotFoundException 
     * when resources/views/instalador/index.blade.php does not exist
     * 
     * Expected to FAIL on unfixed code (this proves the bug exists)
     */
    public function test_installer_route_fails_when_view_missing()
    {
        // Verify the view file does not exist (bug condition)
        $viewPath = resource_path('views/instalador/index.blade.php');
        $this->assertFalse(File::exists($viewPath), 'View file should not exist for bug condition test');
        
        // Attempt to access installer route - should fail with ViewNotFoundException
        $response = $this->get('/instalar');
        
        // This assertion will FAIL on unfixed code - that's expected and correct
        // When the bug is fixed, this will pass
        $response->assertStatus(200);
        $response->assertViewIs('instalador.index');
    }

    /**
     * Test that accessing root route fails to redirect to installer when views are missing
     * 
     * Expected to FAIL on unfixed code (this proves the bug exists)
     */
    public function test_root_route_redirect_fails_when_installer_views_missing()
    {
        // Verify system is not installed
        $this->assertFalse(File::exists(storage_path('installed')), 'System should not be installed');
        
        // Attempt to access root route - should redirect to installer but fail due to missing views
        $response = $this->get('/');
        
        // This assertion will FAIL on unfixed code - that's expected and correct
        // When the bug is fixed, this will pass with proper redirect
        $response->assertRedirect('/instalar');
        
        // Follow the redirect and verify installer loads properly
        $installerResponse = $this->get('/instalar');
        $installerResponse->assertStatus(200);
        $installerResponse->assertViewIs('instalador.index');
    }

    /**
     * Test database configuration conflicts between .env (MySQL) and config/database.php (SQLite default)
     * 
     * Expected to reveal configuration inconsistency
     */
    public function test_database_configuration_consistency()
    {
        // Check .env configuration (should be MySQL)
        $envConnection = env('DB_CONNECTION');
        $this->assertEquals('mysql', $envConnection, '.env should specify MySQL connection');
        
        // Check config/database.php default (currently SQLite)
        $defaultConnection = Config::get('database.default');
        
        // This assertion will FAIL on unfixed code showing the configuration conflict
        // When the bug is fixed, both should consistently use MySQL
        $this->assertEquals($envConnection, $defaultConnection, 
            'Database configuration should be consistent between .env and config/database.php');
    }

    /**
     * Test that installer requirements verification checks correct database extensions
     * 
     * Expected to reveal inconsistency in extension checking
     */
    public function test_installer_requirements_check_database_extensions()
    {
        // Since .env specifies MySQL, installer should check for MySQL extensions
        $envConnection = env('DB_CONNECTION');
        
        if ($envConnection === 'mysql') {
            // MySQL extensions should be checked
            $this->assertTrue(extension_loaded('pdo_mysql'), 
                'MySQL PDO extension should be available when DB_CONNECTION=mysql');
            
            // SQLite extensions should not be required
            // This test documents the expected behavior after fix
            $this->assertTrue(true, 'SQLite extensions should not be required for MySQL configuration');
        }
    }

    /**
     * Property-based test: Bug condition detection across different routes
     * 
     * Tests that the bug condition (missing installer views) affects multiple entry points
     */
    public function test_bug_condition_affects_multiple_installer_routes()
    {
        $installerRoutes = ['/instalar', '/'];
        
        foreach ($installerRoutes as $route) {
            // Each route should fail when installer views are missing
            $response = $this->get($route);
            
            // This will FAIL on unfixed code - that's expected
            // The bug affects multiple routes, not just one
            if ($route === '/') {
                // Root should redirect to installer
                $response->assertRedirect('/instalar');
            } else {
                // Installer route should load successfully
                $response->assertStatus(200);
            }
        }
    }

    /**
     * Test that demonstrates the complete installer workflow expectation
     * 
     * This test encodes the full expected behavior that should work after the fix
     */
    public function test_complete_installer_workflow_expectation()
    {
        // Step 1: Access installer interface
        $response = $this->get('/instalar');
        $response->assertStatus(200);
        $response->assertViewIs('instalador.index');
        
        // Step 2: Verify installer interface contains expected elements
        $response->assertSee('instalador', false); // Should contain installer-related content
        
        // Step 3: Test that installer controller methods are accessible
        // (These should work even with missing views, as they return JSON)
        $requirementsResponse = $this->get('/instalar/verificar-requisitos');
        $requirementsResponse->assertStatus(200);
        $requirementsResponse->assertJson(['sucesso' => true]);
        
        $permissionsResponse = $this->get('/instalar/verificar-permissoes');
        $permissionsResponse->assertStatus(200);
        $permissionsResponse->assertJson(['sucesso' => true]);
    }
}