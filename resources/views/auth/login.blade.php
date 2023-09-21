@extends('layouts.app2')

@section('content')
    <title>Connexion</title>
<style>
body{
        background: #f5f5f5;
        background: -webkit-linear-gradient(to right, #f5f5f5, #d3d3d3);
        background: linear-gradient(to right, #f5f5f5, #d3d3d3);
        height: 100%;
    }
</style>

<section class="vh-100">
  <div class="container py-5">
    <div class="row d-flex justify-content-center align-items-center">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

            <div class="mb-md-5 mt-md-4 ">

              <h2 class="fw-bold mb-2 text-uppercase">Connexion</h2>
              <p class="text-white-50">Entrez votre email et votre mot de passe</p>
              <form method="POST" action="{{ route('login') }}">
                    @csrf

              <div class="form-outline form-white mb-4 mt-4">
                <input id="typeEmailX" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                <label class="form-label" for="typeEmailX">Email</label>
                @error('email')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>

              <div class="form-outline form-white">
                <input id="typePasswordX" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                <label class="form-label" for="typePasswordX">Mot de passe</label>
                @error('password')
                  <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                  </span>
                @enderror
              </div>

              <button class="btn btn-outline-light btn-lg px-5 mt-4" type="submit">Se connecter</button>

            </div>

            <div>
              <p class="mb-0">Vous n'avez pas de compte ? <a href="/register" class="text-white-50 fw-bold">Inscrivez vous</a>
              </p>
            </div>
          </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection