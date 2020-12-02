@extends('layouts.app')

@section('content')
    <div id="connexion">
        <h1 class="title">Kamji</h1>
        <h2 class="subtitle">{{ __('Mot de passe oublié') }}</h2>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.email') }}">
            @csrf

            <label for="email"></label>
            <input id="email" placeholder="E-mail" type="email" class="@error('email') is-invalid @enderror"
                   name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

            @error('email')
            <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror


            <button type="submit" class="btn btn-primary">
                {{ __('Reset mot de passe') }}
            </button>
        </form>
    </div>
    @endsection
    </body>
