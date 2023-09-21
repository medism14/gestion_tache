@extends('layouts.app2')

@section('content')
    <title>Inscription</title>
<style>
body{
        background: #f5f5f5;
        background: -webkit-linear-gradient(to right, #f5f5f5, #d3d3d3);
        background: linear-gradient(to right, #f5f5f5, #d3d3d3);
        height: 100%;
    }
</style>

<section class="vh-100 bg-red">

  <div class="container py-5 px-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 pb-0 text-center">

            <div class="mb-md-5 mt-md-4 ">

              <h2 class="fw-bold mb-2 text-uppercase">Inscription</h2>
              <p class="text-white-50">Entrez les informations suivant</p>
              
            <form method="POST" action="{{ route('user_create') }}">
              @csrf
              <div class="row">
                <div class="col-md-6">
                  <div class="form-outline form-white mb-4">
                    <input id="first_name" type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name') }}" required autocomplete="first_name" autofocus>
                    <label class="form-label" for="first_name">Prenom</label>
                    @error('first_name')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-outline form-white">
                    <input id="last_name" type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name') }}" required autocomplete="last_name" autofocus>
                    <label class="form-label" for="last_name">Nom</label>
                    @error('last_name')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
              </div>

              <div class="row">
                <div class="col-md-6">
                  <div class="form-outline form-white mb-4">
                    <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                    <label class="form-label" for="email">Email</label>
                    @error('email')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-outline form-white">
                    <input id="phone" type="number" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone') }}" required autocomplete="phone" autofocus>
                    <label class="form-label" for="phone">Telephone</label>
                    @error('phone')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>


              <div class="row">
                <div class="col-md-6">
                  <div class="form-outline form-white mb-4">
                    <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                    <label class="form-label" for="password">Mot de passe</label>
                    @error('password')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-outline form-white">
                    <input id="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="current-password">
                    <label class="form-label" for="password_confirmation">Confirmation de mot de passe</label>
                    @error('password_confirmation')
                      <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                      </span>
                    @enderror
                  </div>
                </div>
              </div>

              <button class="btn btn-outline-light btn-lg px-5 mt-2 mb-4" type="submit">S'inscrire</button>

            </div>

            <div style="margin-bottom: 50px;">
              <p class="mb-0">Vous avez un compte ? <a href="/login" class="text-white-50 fw-bold">Connectez vous</a>
              </p>
            </div>
          </form>
          </div>
        </div>
  </div>
</section>

@endsection

