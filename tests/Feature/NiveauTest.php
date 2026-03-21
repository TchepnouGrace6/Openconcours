<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Niveau;
use App\Models\Filiere;
use App\Models\Departement;
use App\Models\Ecole;
use PHPUnit\Framework\Attributes\Test;


class NiveauTest extends TestCase
{    use RefreshDatabase;
 
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
 
    private function creerFiliere(): Filiere
    {
        $ecole       = Ecole::factory()->create();
        $departement = Departement::factory()->create(['ecole_id' => $ecole->id]);
        return Filiere::factory()->create(['departement_id' => $departement->id]);
    }
 
    // =========================================================
    // LISTER LES NIVEAUX (GET /api/niveaux)
    // =========================================================
 
    /*#[Test]
    public function un_utilisateur_connecte_peut_lister_les_niveaux()
    {
        $user    = $this->creerCandidат();
        $filiere = $this->creerFiliere();
 
        Niveau::factory()->count(3)->create(['filiere_id' => $filiere->id]);
 
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/niveaux');
 
        $response->assertStatus(200);
        $this->assertCount(3, $response->json());
    }*/
 
    #[Test]
    public function un_invite_ne_peut_pas_lister_les_niveaux()
    {
        $response = $this->getJson('/api/niveaux');
        $response->assertStatus(401);
    }
 
    #[Test]
    public function la_liste_des_niveaux_est_vide_si_aucun_niveau()
    {
        $user = $this->creerCandidат();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/niveaux');
 
        $response->assertStatus(200)
                 ->assertJson([]);
    }
 
    // =========================================================
    // AFFICHER UN NIVEAU (GET /api/niveaux/{id})
    // =========================================================
 
    #[Test]
    public function un_utilisateur_connecte_peut_voir_un_niveau()
    {
        $user    = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau  = Niveau::factory()->create(['filiere_id' => $filiere->id]);
 
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson("/api/niveaux/{$niveau->id}");
 
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $niveau->id])
                 ->assertJsonFragment(['nom' => $niveau->nom]);
    }
 
    #[Test]
    public function afficher_un_niveau_inexistant_retourne_404()
    {
        $user = $this->creerCandidат();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->getJson('/api/niveaux/99999');
 
        $response->assertStatus(404);
    }
 
    // =========================================================
    // CRÉER UN NIVEAU (POST /api/niveaux)
    // =========================================================
 
    #[Test]
    public function un_utilisateur_connecte_peut_creer_un_niveau()
    {
        $user    = $this->creerAdmin();
        $filiere = $this->creerFiliere();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/niveaux', [
                             'nom'        => 'Licence 1',
                             'filiere_id' => $filiere->id,
                         ]);
 
        $response->assertStatus(201);
 
        $this->assertDatabaseHas('niveaux', [
            'nom'        => 'Licence 1',
            'filiere_id' => $filiere->id,
        ]);
    }
 
    #[Test]
    public function la_creation_niveau_echoue_si_nom_manquant()
    {
        $user    = $this->creerAdmin();
        $filiere = $this->creerFiliere();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/niveaux', [
                             'filiere_id' => $filiere->id,
                         ]);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nom']);
    }
 
    #[Test]
    public function la_creation_niveau_echoue_si_filiere_inexistante()
    {
        $user = $this->creerAdmin();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/niveaux', [
                             'nom'        => 'Licence 1',
                             'filiere_id' => 99999,
                         ]);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['filiere_id']);
    }
 
    #[Test]
    public function la_creation_niveau_echoue_si_champs_manquants()
    {
        $user = $this->creerAdmin();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->postJson('/api/niveaux', []);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['nom', 'filiere_id']);
    }
 
    #[Test]
    public function un_invite_ne_peut_pas_creer_un_niveau()
    {
        $response = $this->postJson('/api/niveaux', [
            'nom'        => 'Licence 1',
            'filiere_id' => 1,
        ]);
 
        $response->assertStatus(401);
    }
 
    // =========================================================
    // MODIFIER UN NIVEAU (PUT /api/niveaux/{id})
    // =========================================================
 
    #[Test]
    public function un_utilisateur_connecte_peut_modifier_un_niveau()
    {
        $user    = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau  = Niveau::factory()->create(['filiere_id' => $filiere->id]);
 
        $response = $this->actingAs($user, 'sanctum')
                         ->putJson("/api/niveaux/{$niveau->id}", [
                             'nom'        => 'Master 2',
                             'filiere_id' => $filiere->id,
                         ]);
 
        $response->assertStatus(200);
 
        $this->assertDatabaseHas('niveaux', [
            'id'  => $niveau->id,
            'nom' => 'Master 2',
        ]);
    }
 
    #[Test]
    public function modifier_un_niveau_inexistant_retourne_404()
    {
        $user = $this->creerAdmin();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->putJson('/api/niveaux/99999', [
                             'nom' => 'Master 2',
                         ]);
 
        $response->assertStatus(404);
    }
 
    #[Test]
    public function un_invite_ne_peut_pas_modifier_un_niveau()
    {
        $filiere = $this->creerFiliere();
        $niveau  = Niveau::factory()->create(['filiere_id' => $filiere->id]);
 
        $response = $this->putJson("/api/niveaux/{$niveau->id}", [
            'nom' => 'Master 2',
        ]);
 
        $response->assertStatus(401);
    }
 
    // =========================================================
    // SUPPRIMER UN NIVEAU (DELETE /api/niveaux/{id})
    // =========================================================
 
   /* #[Test]
    public function un_utilisateur_connecte_peut_supprimer_un_niveau()
    {
        $user    = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau  = Niveau::factory()->create(['filiere_id' => $filiere->id]);
 
        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson("/api/niveaux/{$niveau->id}");
 
        $response->assertStatus(200);
 
        $this->assertDatabaseMissing('niveaux', ['id' => $niveau->id]);
    }*/
 
   /* #[Test]
    public function supprimer_un_niveau_inexistant_retourne_404()
    {
        $user = $this->creerAdmin();
 
        $response = $this->actingAs($user, 'sanctum')
                         ->deleteJson('/api/niveaux/99999');
 
        $response->assertStatus(404);
    }*/
 
    #[Test]
    public function un_invite_ne_peut_pas_supprimer_un_niveau()
    {
        $filiere = $this->creerFiliere();
        $niveau  = Niveau::factory()->create(['filiere_id' => $filiere->id]);
 
        $response = $this->deleteJson("/api/niveaux/{$niveau->id}");
 
        $response->assertStatus(401);
        $this->assertDatabaseHas('niveaux', ['id' => $niveau->id]);
    }
}
