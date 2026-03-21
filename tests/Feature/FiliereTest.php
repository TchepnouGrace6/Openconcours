<?php

namespace Tests\Feature;

use App\Models\Departement;
use App\Models\Ecole;
use App\Models\Filiere;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FiliereTest extends TestCase
{
    use RefreshDatabase;

    // =========================================================
    // Helpers
    // =========================================================

    private function creerAdmin(): Utilisateur
    {
        return Utilisateur::factory()->create(['role' => 'admin']);
    }

    private function creerCandidат(): Utilisateur
    {
        return Utilisateur::factory()->create(['role' => 'candidat']);
    }

    private function creerDepartement(): Departement
    {
        $ecole = Ecole::factory()->create();

        return Departement::factory()->create(['ecole_id' => $ecole->id]);
    }

    // =========================================================
    // LISTER LES FILIÈRES (GET /api/filieres)
    // =========================================================

    /*#[Test]
    public function un_utilisateur_connecte_peut_lister_les_filieres()
    {
        $user        = $this->creerCandidат();
        $departement = $this->creerDepartement();

        Filiere::factory()->count(3)->create(['departement_id' => $departement->id]);

        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/filieres');

        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }*/

    #[Test]
    public function un_invite_ne_peut_pas_lister_les_filieres()
    {
        $response = $this->getJson('/api/filieres');
        $response->assertStatus(401);
    }

    #[Test]
    public function la_liste_des_filieres_est_vide_si_aucune_filiere()
    {
        $user = $this->creerCandidат();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/filieres');

        $response->assertStatus(200)
            ->assertJson([]);
    }

    // =========================================================
    // AFFICHER UNE FILIÈRE (GET /api/filieres/{id})
    // =========================================================

    #[Test]
    public function un_utilisateur_connecte_peut_voir_une_filiere()
    {
        $user = $this->creerCandidат();
        $departement = $this->creerDepartement();
        $filiere = Filiere::factory()->create(['departement_id' => $departement->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/filieres/{$filiere->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $filiere->id])
            ->assertJsonFragment(['nom' => $filiere->nom]);
    }

    #[Test]
    public function afficher_une_filiere_inexistante_retourne_404()
    {
        $user = $this->creerCandidат();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/filieres/99999');

        $response->assertStatus(404);
    }

    #[Test]
    public function un_invite_ne_peut_pas_voir_une_filiere()
    {
        $departement = $this->creerDepartement();
        $filiere = Filiere::factory()->create(['departement_id' => $departement->id]);

        $response = $this->getJson("/api/filieres/{$filiere->id}");
        $response->assertStatus(401);
    }

    // =========================================================
    // CRÉER UNE FILIÈRE (POST /api/filieres)
    // =========================================================

    #[Test]
    public function un_utilisateur_connecte_peut_creer_une_filiere()
    {
        $user = $this->creerAdmin();
        $departement = $this->creerDepartement();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/filieres', [
                'nom' => 'Informatique',
                'description' => 'Filière informatique',
                'departement_id' => $departement->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('filieres', [
            'nom' => 'Informatique',
            'departement_id' => $departement->id,
        ]);
    }

    #[Test]
    public function la_creation_filiere_echoue_si_nom_manquant()
    {
        $user = $this->creerAdmin();
        $departement = $this->creerDepartement();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/filieres', [
                'description' => 'Filière informatique',
                'departement_id' => $departement->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom']);
    }

    #[Test]
    public function la_creation_filiere_echoue_si_departement_inexistant()
    {
        $user = $this->creerAdmin();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/filieres', [
                'nom' => 'Informatique',
                'description' => 'Filière informatique',
                'departement_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['departement_id']);
    }

    #[Test]
    public function la_creation_filiere_echoue_si_champs_manquants()
    {
        $user = $this->creerAdmin();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/filieres', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'departement_id']);
    }

    #[Test]
    public function un_invite_ne_peut_pas_creer_une_filiere()
    {
        $response = $this->postJson('/api/filieres', [
            'nom' => 'Informatique',
            'description' => 'Filière informatique',
            'departement_id' => 1,
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function deux_filieres_ne_peuvent_pas_avoir_le_meme_nom()
    {
        $user = $this->creerAdmin();
        $departement = $this->creerDepartement();

        Filiere::factory()->create([
            'nom' => 'Informatique',
            'departement_id' => $departement->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/filieres', [
                'nom' => 'Informatique',
                'description' => 'Autre description',
                'departement_id' => $departement->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom']);
    }

    // =========================================================
    // MODIFIER UNE FILIÈRE (PUT /api/filieres/{id})
    // =========================================================

    #[Test]
    public function un_utilisateur_connecte_peut_modifier_une_filiere()
    {
        $user = $this->creerAdmin();
        $departement = $this->creerDepartement();
        $filiere = Filiere::factory()->create(['departement_id' => $departement->id]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson("/api/filieres/{$filiere->id}", [
                'nom' => 'Génie Logiciel',
                'description' => 'Nouvelle description',
                'departement_id' => $departement->id,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('filieres', [
            'id' => $filiere->id,
            'nom' => 'Génie Logiciel',
        ]);
    }

    #[Test]
    public function modifier_une_filiere_inexistante_retourne_404()
    {
        $user = $this->creerAdmin();

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/filieres/99999', [
                'nom' => 'Génie Logiciel',
            ]);

        $response->assertStatus(404);
    }

    #[Test]
    public function un_invite_ne_peut_pas_modifier_une_filiere()
    {
        $departement = $this->creerDepartement();
        $filiere = Filiere::factory()->create(['departement_id' => $departement->id]);

        $response = $this->putJson("/api/filieres/{$filiere->id}", [
            'nom' => 'Génie Logiciel',
        ]);

        $response->assertStatus(401);
    }

    // =========================================================
    // SUPPRIMER UNE FILIÈRE (DELETE /api/filieres/{id})
    // =========================================================

    /*#[Test]
    public function un_utilisateur_connecte_peut_supprimer_une_filiere()
    {
        $user        = $this->creerAdmin();
        $departement = $this->creerDepartement();
        $filiere     = Filiere::factory()->create(['departement_id' => $departement->id]);

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson("/api/filieres/{$filiere->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('filieres', ['id' => $filiere->id]);
    }*/

    /*#[Test]
    public function supprimer_une_filiere_inexistante_retourne_404()
    {
        $user = $this->creerAdmin();

        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/filieres/99999');

        $response->assertStatus(404);
    }*/

    #[Test]
    public function un_invite_ne_peut_pas_supprimer_une_filiere()
    {
        $departement = $this->creerDepartement();
        $filiere = Filiere::factory()->create(['departement_id' => $departement->id]);

        $response = $this->deleteJson("/api/filieres/{$filiere->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('filieres', ['id' => $filiere->id]);
    }
}
