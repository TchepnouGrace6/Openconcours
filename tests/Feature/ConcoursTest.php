<?php

namespace Tests\Feature;

use App\Models\Concours;
use App\Models\Departement;
use App\Models\Ecole;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Utilisateur;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ConcoursTest extends TestCase
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

    private function creerFiliere(): Filiere
    {
        $ecole = Ecole::factory()->create();
        $departement = Departement::factory()->create(['ecole_id' => $ecole->id]);

        return Filiere::factory()->create(['departement_id' => $departement->id]);
    }

    private function creerNiveau(Filiere $filiere): Niveau
    {
        return Niveau::factory()->create(['filiere_id' => $filiere->id]);
    }

    private function payloadConcours(Filiere $filiere, Niveau $niveau): array
    {
        return [
            'nom' => 'Concours Informatique 2024',
            'description' => 'Description du concours',
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
            'date_concours' => now()->addMonths(3)->toDateString(),
            'date_limite_dossier' => now()->addMonth()->toDateString(),
            'date_limite_paiement' => now()->addMonths(2)->toDateString(),
            'taux_reussite' => 60,
            'taux_echec' => 40,
        ];
    }

    // =========================================================
    // LISTER LES CONCOURS (GET /api/concours)
    // =========================================================

    #[Test]
    public function un_utilisateur_connecte_peut_lister_les_concours()
    {
        $user = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        Concours::factory()->count(3)->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/concours');

        $response->assertStatus(200)
            ->assertJsonStructure(['data', 'total', 'per_page']);

        $this->assertCount(3, $response->json('data'));
    }

    #[Test]
    public function la_liste_des_concours_est_paginee_par_10()
    {
        $user = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        Concours::factory()->count(15)->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/concours');

        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }

    #[Test]
    public function un_invite_ne_peut_pas_lister_les_concours()
    {
        $response = $this->getJson('/api/concours');
        $response->assertStatus(401);
    }

    #[Test]
    public function la_liste_inclut_la_filiere_et_le_niveau()
    {
        $user = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/concours');

        $response->assertStatus(200);
        $this->assertArrayHasKey('filiere', $response->json('data.0'));
        $this->assertArrayHasKey('niveau', $response->json('data.0'));
    }

    // =========================================================
    // AFFICHER UN CONCOURS (GET /api/concours/{id})
    // =========================================================

    #[Test]
    public function un_utilisateur_connecte_peut_voir_un_concours()
    {
        $user = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);
        $concours = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson("/api/concours/{$concours->id}");

        $response->assertStatus(200)
            ->assertJsonFragment(['id' => $concours->id])
            ->assertJsonFragment(['nom' => $concours->nom]);
    }

    #[Test]
    public function afficher_un_concours_inexistant_retourne_404()
    {
        $user = $this->creerCandidат();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/concours/99999');

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Concours non trouvé']);
    }

    #[Test]
    public function un_invite_ne_peut_pas_voir_un_concours()
    {
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);
        $concours = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->getJson("/api/concours/{$concours->id}");
        $response->assertStatus(401);
    }

    // =========================================================
    // CRÉER UN CONCOURS (POST /api/concours)
    // =========================================================

    #[Test]
    public function un_admin_peut_creer_un_concours()
    {
        $admin = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/concours', $this->payloadConcours($filiere, $niveau));

        $response->assertStatus(201)
            ->assertJsonFragment(['message' => 'Concours créé avec succès'])
            ->assertJsonStructure(['concours' => ['id', 'nom', 'filiere_id', 'niveau_id']]);

        $this->assertDatabaseHas('concours', [
            'nom' => 'Concours Informatique 2024',
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);
    }

    #[Test]
    public function un_admin_peut_creer_un_concours_sans_champs_optionnels()
    {
        $admin = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/concours', [
                'nom' => 'Concours Minimal',
                'filiere_id' => $filiere->id,
                'niveau_id' => $niveau->id,
            ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('concours', ['nom' => 'Concours Minimal']);
    }

    #[Test]
    public function un_candidat_ne_peut_pas_creer_un_concours()
    {
        $candidat = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        $response = $this->actingAs($candidat, 'sanctum')
            ->postJson('/api/concours', $this->payloadConcours($filiere, $niveau));

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Accès refusé. Admin uniquement.']);
    }

    #[Test]
    public function un_invite_ne_peut_pas_creer_un_concours()
    {
        $response = $this->postJson('/api/concours', [
            'nom' => 'Concours Test',
        ]);

        $response->assertStatus(401);
    }

    #[Test]
    public function la_creation_concours_echoue_si_nom_manquant()
    {
        $admin = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/concours', [
                'filiere_id' => $filiere->id,
                'niveau_id' => $niveau->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom']);
    }

    #[Test]
    public function la_creation_concours_echoue_si_filiere_inexistante()
    {
        $admin = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/concours', [
                'nom' => 'Concours Test',
                'filiere_id' => 99999,
                'niveau_id' => $niveau->id,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['filiere_id']);
    }

    #[Test]
    public function la_creation_concours_echoue_si_niveau_inexistant()
    {
        $admin = $this->creerAdmin();
        $filiere = $this->creerFiliere();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/concours', [
                'nom' => 'Concours Test',
                'filiere_id' => $filiere->id,
                'niveau_id' => 99999,
            ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['niveau_id']);
    }

    #[Test]
    public function la_creation_concours_echoue_si_champs_obligatoires_manquants()
    {
        $admin = $this->creerAdmin();

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/concours', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['nom', 'filiere_id', 'niveau_id']);
    }

    // =========================================================
    // MODIFIER UN CONCOURS (PUT /api/concours/{id})
    // =========================================================

    #[Test]
    public function un_admin_peut_modifier_un_concours()
    {
        $admin = $this->creerAdmin();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);
        $concours = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson("/api/concours/{$concours->id}", [
                'nom' => 'Concours Modifié',
                'filiere_id' => $filiere->id,
                'niveau_id' => $niveau->id,
            ]);

        $response->assertStatus(200)
            ->assertJsonFragment(['message' => 'Concours mis à jour avec succès']);

        $this->assertDatabaseHas('concours', [
            'id' => $concours->id,
            'nom' => 'Concours Modifié',
        ]);
    }

    #[Test]
    public function un_candidat_ne_peut_pas_modifier_un_concours()
    {
        $candidat = $this->creerCandidат();
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);
        $concours = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->actingAs($candidat, 'sanctum')
            ->putJson("/api/concours/{$concours->id}", [
                'nom' => 'Concours Modifié',
            ]);

        $response->assertStatus(403)
            ->assertJsonFragment(['message' => 'Accès refusé. Admin uniquement.']);
    }

    #[Test]
    public function modifier_un_concours_inexistant_retourne_404()
    {
        $admin = $this->creerAdmin();

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/concours/99999', [
                'nom' => 'Concours Modifié',
            ]);

        $response->assertStatus(404)
            ->assertJsonFragment(['message' => 'Concours non trouvé']);
    }

    #[Test]
    public function un_invite_ne_peut_pas_modifier_un_concours()
    {
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);
        $concours = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->putJson("/api/concours/{$concours->id}", [
            'nom' => 'Concours Modifié',
        ]);

        $response->assertStatus(401);
    }

    // =========================================================
    // SUPPRIMER UN CONCOURS (DELETE /api/concours/{id})
    // =========================================================
    /*
       #[Test]
       public function un_admin_peut_supprimer_un_concours()
       {
           $admin    = $this->creerAdmin();
           $filiere  = $this->creerFiliere();
           $niveau   = $this->creerNiveau($filiere);
           $concours = Concours::factory()->create([
               'filiere_id' => $filiere->id,
               'niveau_id'  => $niveau->id,
           ]);

           $response = $this->actingAs($admin, 'sanctum')
                            ->deleteJson("/api/concours/{$concours->id}");

           $response->assertStatus(200)
                    ->assertJsonFragment(['message' => 'Concours supprimé avec succès']);

           $this->assertDatabaseMissing('concours', ['id' => $concours->id]);
       }

       #[Test]
       public function un_candidat_ne_peut_pas_supprimer_un_concours()
       {
           $candidat = $this->creerCandidат();
           $filiere  = $this->creerFiliere();
           $niveau   = $this->creerNiveau($filiere);
           $concours = Concours::factory()->create([
               'filiere_id' => $filiere->id,
               'niveau_id'  => $niveau->id,
           ]);

           $response = $this->actingAs($candidat, 'sanctum')
                            ->deleteJson("/api/concours/{$concours->id}");

           $response->assertStatus(403);
           $this->assertDatabaseHas('concours', ['id' => $concours->id]);
       }

       #[Test]
       public function supprimer_un_concours_inexistant_retourne_404()
       {
           $admin = $this->creerAdmin();

           $response = $this->actingAs($admin, 'sanctum')
                            ->deleteJson('/api/concours/99999');

           $response->assertStatus(404)
                    ->assertJsonFragment(['message' => 'Concours non trouvé']);
       }*/

    #[Test]
    public function un_invite_ne_peut_pas_supprimer_un_concours()
    {
        $filiere = $this->creerFiliere();
        $niveau = $this->creerNiveau($filiere);
        $concours = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id' => $niveau->id,
        ]);

        $response = $this->deleteJson("/api/concours/{$concours->id}");

        $response->assertStatus(401);
        $this->assertDatabaseHas('concours', ['id' => $concours->id]);
    }
}
