@extends('layouts.app')

@section('content')
    <div class="error-404">
        <section class="hero full-height" data-anim-in="true" scrolled-in="true">
            <div class="hero__content wrap wrap--wide">
                <h1 style="font-weight: bold;font-size: 80px">404</h1>
                <h2 style="font-weight: bold;font-size: 20px">La page n'existe pas encore</h2>
            </div>
            <div class="wrap wrap--404-img">
                <div class="error-404__img">
                    <img src="{{ asset('images/bongo-cat.gif') }}" alt="bongo-cat" width="400px">
                </div>
            </div>
            <a href=javascript:history.go(-1) class="button-404">
                <span>Retour</span>
            </a>
        </section>
    </div>
@endsection
