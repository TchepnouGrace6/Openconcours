<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;  // ← manquant !
use App\Models\Utilisateur;
use Tests\TestCase;

class AuthTest extends TestCase
{
        use RefreshDatabase;
 
    // =========================================================
    // INSCRIPTION (POST /api/register)
    // =========================================================
 
    #[Test]
    public function un_candidat_peut_sinscrire_avec_des_donnees_valides()
    {
        $response = $this->postJson('/api/register', [
            'nom'      => 'Jean Dupont',
            'email'    => 'jean@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'Compte candidat créé avec succès'])
                 ->assertJsonPath('utilisateur.role', 'candidat')
                 ->assertJsonPath('utilisateur.email', 'jean@example.com');
 
        $this->assertDatabaseHas('utilisateurs', [
            'email' => 'jean@example.com',
            'role'  => 'candidat',
        ]);
    }
 
    #[Test]
    public function linscription_echoue_si_email_deja_utilise()
    {
        Utilisateur::factory()->create(['email' => 'jean@example.com']);
 
        $response = $this->postJson('/api/register', [
            'nom'      => 'Jean Dupont',
            'email'    => 'jean@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
 
    #[Test]
    public function linscription_echoue_si_champs_obligatoires_manquants()
    {
        $response = $this->postJson('/api/register', []);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nom', 'email', 'password']);
    }
 
    #[Test]
    public function linscription_echoue_si_mot_de_passe_trop_court()
    {
        $response = $this->postJson('/api/register', [
            'nom'      => 'Jean Dupont',
            'email'    => 'jean@example.com',
            'password' => '123',
        ]);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['password']);
    }
 
    #[Test]
    public function linscription_echoue_si_email_invalide()
    {
        $response = $this->postJson('/api/register', [
            'nom'      => 'Jean Dupont',
            'email'    => 'pas-un-email',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }
 
    #[Test]
    public function le_mot_de_passe_nest_pas_retourne_apres_inscription()
    {
        $response = $this->postJson('/api/register', [
            'nom'      => 'Jean Dupont',
            'email'    => 'jean@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(201);
        $this->assertArrayNotHasKey('password', $response->json('utilisateur'));
    }
 
    // =========================================================
    // CONNEXION (POST /api/login)
    // =========================================================
 
    #[Test]
    public function un_utilisateur_peut_se_connecter_avec_de_bons_identifiants()
    {
        Utilisateur::factory()->create([
            'email'    => 'jean@example.com',
            'password' => bcrypt('Password123!'),
        ]);
 
        $response = $this->postJson('/api/login', [
            'email'    => 'jean@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Connexion réussie'])
                 ->assertJsonStructure(['access_token', 'token_type', 'user']);
    }
 
    #[Test]
    public function la_connexion_echoue_avec_un_mauvais_mot_de_passe()
    {
        Utilisateur::factory()->create([
            'email'    => 'jean@example.com',
            'password' => bcrypt('Password123!'),
        ]);
 
        $response = $this->postJson('/api/login', [
            'email'    => 'jean@example.com',
            'password' => 'mauvais_mdp',
        ]);
 
        $response->assertStatus(401)
                 ->assertJsonFragment(['message' => 'Identifiants invalides']);
    }
 
    #[Test]
    public function la_connexion_echoue_si_email_inexistant()
    {
        $response = $this->postJson('/api/login', [
            'email'    => 'inconnu@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(401)
                 ->assertJsonFragment(['message' => 'Identifiants invalides']);
    }
 
    #[Test]
    public function la_connexion_echoue_si_champs_manquants()
    {
        $response = $this->postJson('/api/login', []);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email', 'password']);
    }
 
    #[Test]
    public function la_connexion_retourne_un_token_bearer()
    {
        Utilisateur::factory()->create([
            'email'    => 'jean@example.com',
            'password' => bcrypt('Password123!'),
        ]);
 
        $response = $this->postJson('/api/login', [
            'email'    => 'jean@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(200)
                 ->assertJsonFragment(['token_type' => 'Bearer']);
 
        $this->assertNotNull($response->json('access_token'));
    }
 
    // =========================================================
    // CRÉATION ADMIN (POST /api/create-admin)
    // =========================================================
 
    #[Test]
    public function un_admin_peut_creer_un_autre_admin()
    {
        $admin = Utilisateur::factory()->create(['role' => 'admin']);
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->postJson('/api/create-admin', [
                             'nom'      => 'Super Admin',
                             'email'    => 'admin2@example.com',
                             'password' => 'AdminPass123!',
                         ]);
 
        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'Administrateur créé avec succès'])
                 ->assertJsonPath('utilisateur.role', 'admin');
 
        $this->assertDatabaseHas('utilisateurs', [
            'email' => 'admin2@example.com',
            'role'  => 'admin',
        ]);
    }
 
    #[Test]
    public function un_candidat_ne_peut_pas_creer_un_admin()
    {
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/create-admin', [
                             'nom'      => 'Faux Admin',
                             'email'    => 'faux@example.com',
                             'password' => 'Password123!',
                         ]);
 
        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Accès refusé']);
    }
 
    #[Test]
    public function un_invite_ne_peut_pas_creer_un_admin()
    {
        $response = $this->postJson('/api/create-admin', [
            'nom'      => 'Hacker',
            'email'    => 'hacker@example.com',
            'password' => 'Password123!',
        ]);
 
        $response->assertStatus(401);
    }
}
