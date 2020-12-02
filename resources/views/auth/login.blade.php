@extends('layouts.app')

@section('content')
    <div id="connexion">
        <h1 class="title">Kamji</h1>
        <h2 class="subtitle">{{ __('Connexion') }}</h2>
        <form method="POST" action="{{ route('login') }}">
            @csrf

            <label for="email"></label>
            <input id="email" placeholder="E-mail" type="email" class="@error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="password"></label>
            <input id="password" placeholder="Mot de passe" type="password"
                   class="@error('password') is-invalid @enderror" name="password" required
                   autocomplete="current-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <div class="form-check" style="padding-top: 20px">
                <input class="form-check-input" type="checkbox" name="remember"
                       id="remember" {{ old('remember') ? 'checked' : '' }}>

                <label class="form-check-label" for="remember">
                    {{ __('Se souvenir de moi') }}
                </label>
            </div>
            <button type="submit" class="btn btn-primary">
                {{ __('Connexion') }}
            </button>
        </form>
    </div>
    @endsection
    </body>
