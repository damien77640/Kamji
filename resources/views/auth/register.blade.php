@extends('layouts.app')

@section('content')
    <div id="connexion">
        <h1 class="title">Kamji</h1>
        <h2 class="subtitle">{{ __('Inscription') }}</h2>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <label for="name"></label>
            <input id="name" placeholder="Pseudonyme" type="text" class="@error('name') is-invalid @enderror"
                   name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

            @error('name')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="email"></label>
            <input id="email" placeholder="E-mail" type="email" class="@error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required autocomplete="email">
            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="password"></label>
            <input id="password" placeholder="Mot de passe" type="password"
                   class="@error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
            @error('password')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror

            <label for="password-confirm"></label>
            <input id="password-confirm" placeholder="Confirmation mot de passe" type="password"
                   name="password_confirmation" required autocomplete="new-password">

            <button type="submit" class="btn btn-primary">
                {{ __("S'inscrire") }}
            </button>
        </form>
    </div>
    @endsection
    </body>
