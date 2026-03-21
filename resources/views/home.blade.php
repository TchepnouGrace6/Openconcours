@extends('layouts.app')

@section('title', 'Accueil')

@section('content')

<!-- HERO -->
<section class="bg-primary text-white py-5" style="background: linear-gradient(135deg, #4e73df, #1cc88a);">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Bienvenue sur OpenConcours</h1>
        <p class="lead mt-3">
            La plateforme qui facilite la gestion des concours, candidatures et opportunités.
        </p>
        <a href="{{ route('login') }}" class="btn btn-light btn-lg mt-4 shadow-sm">
            Se connecter
        </a>
    </div>
</section>

<!-- A PROPOS -->
<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6">
                <h2 class="fw-bold">À propos de la plateforme</h2>
                <p class="text-muted mt-3">
                    Notre objectif est de simplifier l’accès à l’information, la gestion des concours
                    et la mise en relation entre candidats et organisateurs.
                </p>
                <a href="{{ route('register') }}" class="btn btn-primary mt-3 shadow-sm">Créer un compte</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="{{ asset('images/hero.png') }}" class="img-fluid rounded shadow-sm" alt="Illustration">
            </div>
        </div>
    </div>
</section>

<!-- SERVICES / FONCTIONNALITES -->
<section class="bg-light py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-5">Ce que nous proposons</h2>
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 shadow border-0 rounded-4">
                    <div class="card-body text-center">
                        <h5 class="fw-bold mb-3">📢 Publication de concours</h5>
                        <p class="text-muted">Diffusion rapide et efficace des opportunités pour tous les candidats.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow border-0 rounded-4">
                    <div class="card-body text-center">
                        <h5 class="fw-bold mb-3">📝 Candidature en ligne</h5>
                        <p class="text-muted">Postulez facilement depuis votre espace personnel en quelques clics.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 shadow border-0 rounded-4">
                    <div class="card-body text-center">
                        <h5 class="fw-bold mb-3">📊 Suivi des dossiers</h5>
                        <p class="text-muted">Visualisez l’état de vos candidatures en temps réel, où que vous soyez.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CALL TO ACTION -->
<section class="py-5 text-center" style="background-color: #f8f9fa;">
    <div class="container">
        <h2 class="fw-bold">Prêt à commencer ?</h2>
        <p class="text-muted mt-2">Créez votre compte et accédez aux opportunités dès aujourd’hui.</p>
        <a href="{{ route('register') }}" class="btn btn-primary btn-lg mt-3 shadow-sm">
            Créer un compte
        </a>
    </div>
</section>

@endsection
