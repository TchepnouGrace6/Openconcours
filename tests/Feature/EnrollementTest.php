<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Utilisateur;
use App\Models\Enrollement;
use App\Models\Paiement;
use App\Models\ConcoursSession;
use App\Models\Concours;
use App\Models\CentreExamen;
use App\Models\CentreDepot;
use App\Models\Salle;
use App\Models\Filiere;
use App\Models\Niveau;
use App\Models\Departement;
use App\Models\Ecole;
use Illuminate\Support\Facades\Mail;
use PHPUnit\Framework\Attributes\Test;


class EnrollementTest extends TestCase
{
       use RefreshDatabase;
 
       /*private function creerContexteComplet(): array
    {
        $ecole       = Ecole::factory()->create();
        $departement = Departement::factory()->create(['ecole_id' => $ecole->id]);
        $filiere     = Filiere::factory()->create(['departement_id' => $departement->id]);
        $niveau      = Niveau::factory()->create(['filiere_id' => $filiere->id]);
        $concours    = Concours::factory()->create([
            'filiere_id' => $filiere->id,
            'niveau_id'  => $niveau->id,
        ]);
 
        $centreExamen = CentreExamen::factory()->create();
 
        // Salle liée au centre d'examen
        Salle::factory()->create([
            'centres_examen_id' => $centreExamen->id,
            'capacite'          => 30,
        ]);
 
        // Session liée au concours et au centre
        $session = ConcoursSession::factory()->create([
            'concours_id'       => $concours->id,
            'centres_examen_id' => $centreExamen->id,
        ]);
 
        // Centre dépôt lié au concours
        $centreDepot = CentreDepot::factory()->create([
            'concours_id' => $concours->id,
        ]);
 
        // Lier le centre d'examen au concours via la table pivot
        $concours->centres()->attach($centreExamen->id);
 
        return compact('session', 'centreExamen', 'centreDepot', 'concours');
    }
 
    /**
     * Crée un paiement avec statut "paye" pour un candidat donné//
    
    private function creerPaiementValide(Utilisateur $user, ConcoursSession $session): Paiement
    {
        return Paiement::factory()->create([
            'utilisateur_id'      => $user->id,
            'concours_session_id' => $session->id,
            'statut'              => 'paye',
            'numero_recu'         => 'REC-' . now()->year . '-000001',
        ]);
    }*/
 
    /**
     * Payload complet pour créer un enrôlement valide
     */
    /*private function payloadEnrollement(array $ctx, string $numeroRecu): array
    {
        return [
            'concours_session_id' => $ctx['session']->id,
            'centre_examen_id'    => $ctx['centreExamen']->id,
            'centre_depot_id'     => $ctx['centreDepot']->id,
            'numero_recu'         => $numeroRecu,
            'prenom'              => 'Marie',
            'nom'                 => 'Nguema',
            'date_naissance'      => '2000-05-12',
            'sexe'                => 'feminin',
            'telephone'           => '699000001',
            'adresse'             => '123 Rue Exemple',
            'lieu_residence'      => 'Yaoundé',
            'nationalite'         => 'Camerounaise',
            'numero_cni'          => '123456789',
            'date_delivrance_cni' => '2018-01-01',
            'region_origine'      => 'Centre',
            'nom_pere'            => 'Pierre Nguema',
            'nom_mere'            => 'Claire Nguema',
            'telephone_pere'      => '699111111',
            'telephone_mere'      => '699222222',
            'statut_matrimoniale' => 'celibataire',
            'niveau_etude'        => 'Licence',
            'est_handicape'       => false,
        ];
    }*/
 
    // =========================================================
    // CRÉATION ENRÔLEMENT (POST /api/enrollements)
    // =========================================================
 
    /*#[Test]
    public function un_candidat_peut_senroler_avec_un_paiement_valide()
    {
        Mail::fake();
 
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
        $ctx      = $this->creerContexteComplet();
        $paiement = $this->creerPaiementValide($candidat, $ctx['session']);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', $this->payloadEnrollement($ctx, $paiement->numero_recu));
 
        $response->assertStatus(201)
                 ->assertJsonFragment(['message' => 'Enrôlement effectué avec succès'])
                 ->assertJsonStructure([
                     'enrollement' => [
                         'numero_enrollement',
                         'numero_table',
                         'statut',
                     ],
                 ]);
 
        $this->assertDatabaseHas('enrollements', [
            'utilisateur_id'      => $candidat->id,
            'concours_session_id' => $ctx['session']->id,
            'statut'              => 'en_attente',
            'numero_recu'         => $paiement->numero_recu,
        ]);
    }*/
 
   /* #[Test]
    public function le_numero_enrollement_commence_par_ENR()
    {
        Mail::fake();
 
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
        $ctx      = $this->creerContexteComplet();
        $paiement = $this->creerPaiementValide($candidat, $ctx['session']);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', $this->payloadEnrollement($ctx, $paiement->numero_recu));
 
        $response->assertStatus(201);
        $this->assertStringStartsWith('ENR-', $response->json('enrollement.numero_enrollement'));
    }
 
    #[Test]
    public function le_statut_initial_est_en_attente()
    {
        Mail::fake();
 
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
        $ctx      = $this->creerContexteComplet();
        $paiement = $this->creerPaiementValide($candidat, $ctx['session']);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', $this->payloadEnrollement($ctx, $paiement->numero_recu));
 
        $response->assertStatus(201)
                 ->assertJsonPath('enrollement.statut', 'en_attente');
    }
 
    #[Test]
    public function lenrolement_echoue_sans_paiement_valide()
    {
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
        $ctx      = $this->creerContexteComplet();
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', $this->payloadEnrollement($ctx, 'REC-FAUX-999'));
 
        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Numéro de reçu invalide ou paiement non confirmé']);
    }
 
    #[Test]
    public function un_recu_ne_peut_pas_etre_utilise_deux_fois()
    {
        Mail::fake();
 
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
        $ctx      = $this->creerContexteComplet();
        $paiement = $this->creerPaiementValide($candidat, $ctx['session']);
 
        // Premier enrôlement — doit réussir
        $this->actingAs($candidat, 'sanctum')
             ->postJson('/api/enrollements', $this->payloadEnrollement($ctx, $paiement->numero_recu))
             ->assertStatus(201);
 
        // Deuxième tentative avec le même reçu — doit échouer
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', $this->payloadEnrollement($ctx, $paiement->numero_recu));
 
        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Ce numéro de reçu a déjà servi à un enrôlement']);
    }
 
    #[Test]
    public function lenrolement_echoue_si_centre_non_lie_au_concours()
    {
        $candidat    = Utilisateur::factory()->create(['role' => 'candidat']);
        $ctx         = $this->creerContexteComplet();
        $paiement    = $this->creerPaiementValide($candidat, $ctx['session']);
        $autreCentre = CentreExamen::factory()->create();
 
        $payload = $this->payloadEnrollement($ctx, $paiement->numero_recu);
        $payload['centre_examen_id'] = $autreCentre->id;
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', $payload);
 
        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Centre invalide']);
    }*/
 
    #[Test]
    public function lenrolement_echoue_si_champs_obligatoires_manquants()
    {
        $candidat = Utilisateur::factory()->create(['role' => 'candidat']);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->postJson('/api/enrollements', []);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'concours_session_id',
                     'centre_examen_id',
                     'centre_depot_id',
                     'numero_recu',
                     'prenom',
                     'nom',
                     'date_naissance',
                     'sexe',
                     'telephone',
                 ]);
    }
 
    #[Test]
    public function un_invite_ne_peut_pas_senroler()
    {
        $response = $this->postJson('/api/enrollements', []);
        $response->assertStatus(401);
    }
 
    // =========================================================
    // AFFICHER UN ENRÔLEMENT (GET /api/enrollements/{id})
    // =========================================================
 
   /* #[Test]
    public function un_candidat_peut_voir_son_enrollement()
    {
        $candidat    = Utilisateur::factory()->create(['role' => 'candidat']);
        $enrollement = Enrollement::factory()->create(['utilisateur_id' => $candidat->id]);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->getJson("/api/enrollements/{$enrollement->id}");
 
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $enrollement->id]);
    }*/
 
    #[Test]
    public function afficher_un_enrollement_inexistant_retourne_404()
    {
        $admin = Utilisateur::factory()->create(['role' => 'admin']);
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/enrollements/99999');
 
        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Enrôlement non trouvé']);
    }
 
    // =========================================================
    // LISTE DES ENRÔLEMENTS (GET /api/enrollements)
    // =========================================================
 
   /* #[Test]
    public function un_admin_peut_lister_tous_les_enrollements()
    {
        $admin = Utilisateur::factory()->create(['role' => 'admin']);
        Enrollement::factory()->count(5)->create();
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/enrollements');
 
        $response->assertStatus(200)
                 ->assertJsonStructure(['data', 'total', 'per_page']);
    }
 
    #[Test]
    public function la_liste_est_paginee_par_10()
    {
        $admin = Utilisateur::factory()->create(['role' => 'admin']);
        Enrollement::factory()->count(15)->create();
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->getJson('/api/enrollements');
 
        $response->assertStatus(200);
        $this->assertCount(10, $response->json('data'));
    }*/
 
    // =========================================================
    // VALIDER / REFUSER (PUT /api/enrollements/{id})
    // =========================================================
 
    /*#[Test]
    public function un_admin_peut_valider_un_enrollement()
    {
        Mail::fake();
 
        $admin       = Utilisateur::factory()->create(['role' => 'admin']);
        $candidat    = Utilisateur::factory()->create(['role' => 'candidat']);
        $enrollement = Enrollement::factory()->create([
            'utilisateur_id' => $candidat->id,
            'statut'         => 'en_attente',
        ]);
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson("/api/enrollements/{$enrollement->id}", [
                             'statut' => 'valide',
                         ]);
 
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Enrôlement mis à jour avec succès']);
 
        $this->assertDatabaseHas('enrollements', [
            'id'     => $enrollement->id,
            'statut' => 'valide',
        ]);
    }*/
 
   /* #[Test]
    public function un_admin_peut_refuser_un_enrollement()
    {
        Mail::fake();
 
        $admin       = Utilisateur::factory()->create(['role' => 'admin']);
        $enrollement = Enrollement::factory()->create(['statut' => 'en_attente']);
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson("/api/enrollements/{$enrollement->id}", [
                             'statut' => 'refuse',
                         ]);
 
        $response->assertStatus(200);
        $this->assertDatabaseHas('enrollements', [
            'id'     => $enrollement->id,
            'statut' => 'refuse',
        ]);
    }*/
 
    /*#[Test]
    public function un_candidat_ne_peut_pas_modifier_le_statut()
    {
        $candidat    = Utilisateur::factory()->create(['role' => 'candidat']);
        $enrollement = Enrollement::factory()->create(['statut' => 'en_attente']);
 
        $response = $this->actingAs($candidat, 'sanctum')
                         ->putJson("/api/enrollements/{$enrollement->id}", [
                             'statut' => 'valide',
                         ]);
 
        $response->assertStatus(403)
                 ->assertJsonFragment(['message' => 'Accès refusé. Admin uniquement.']);
    }*/
 
    /*#[Test]
    public function le_statut_doit_etre_une_valeur_autorisee()
    {
        $admin       = Utilisateur::factory()->create(['role' => 'admin']);
        $enrollement = Enrollement::factory()->create(['statut' => 'en_attente']);
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson("/api/enrollements/{$enrollement->id}", [
                             'statut' => 'statut_invalide',
                         ]);
 
        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['statut']);
    }*/
 
    #[Test]
    public function modifier_un_enrollement_inexistant_retourne_404()
    {
        $admin = Utilisateur::factory()->create(['role' => 'admin']);
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->putJson('/api/enrollements/99999', [
                             'statut' => 'valide',
                         ]);
 
        $response->assertStatus(404)
                 ->assertJsonFragment(['message' => 'Enrôlement non trouvé']);
    }
 
    // =========================================================
    // SUPPRESSION (DELETE /api/enrollements/{id})
    // =========================================================
 
    /*#[Test]
    public function un_admin_peut_supprimer_un_enrollement()
    {
        $admin       = Utilisateur::factory()->create(['role' => 'admin']);
        $enrollement = Enrollement::factory()->create();
 
        $response = $this->actingAs($admin, 'sanctum')
                         ->deleteJson("/api/enrollements/{$enrollement->id}");
 
        $response->assertStatus(200)
                 ->assertJsonFragment(['message' => 'Enrôlement supprimé']);
 
        $this->assertDatabaseMissing('enrollements', ['id' => $enrollement->id]);
    }*/
 
    /*#[Test]
    public function un_invite_ne_peut_pas_supprimer_un_enrollement()
    {
        $enrollement = Enrollement::factory()->create();
 
        $response = $this->deleteJson("/api/enrollements/{$enrollement->id}");
 
        $response->assertStatus(401);
        $this->assertDatabaseHas('enrollements', ['id' => $enrollement->id]);
    }*/
}
