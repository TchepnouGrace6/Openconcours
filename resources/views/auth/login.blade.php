@extends('layouts.app')

@section('title', 'Connexion')

@section('content')
<section class="py-5" style="background-color: #f8f9fa;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <h3 class="fw-bold text-center mb-4">Se connecter</h3>

                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="mb-3">
                                <label for="email" class="form-label">Adresse email</label>
                                <input type="email" name="email" id="email" class="form-control" required autofocus>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mot de passe</label>
                                <input type="password" name="password" id="password" class="form-control" required>
                            </div>

                            <div class="mb-3 form-check">
                                <input type="checkbox" name="remember" id="remember" class="form-check-input">
                                <label class="form-check-label" for="remember">Se souvenir de moi</label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mt-3 shadow-sm">
                                Connexion
                            </button>
                        </form>

                        <p class="text-center mt-3">
                            Pas encore de compte ? <a href="{{ route('register') }}">S’inscrire</a>
                        </p>

                        <p class="text-center text-muted small mt-2">
                            <a href="#">Mot de passe oublié ?</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
