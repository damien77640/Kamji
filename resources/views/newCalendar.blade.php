@extends('layouts.header')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <form class="contact100-form validate-form" action="{{ action('CalendarController@insert') }}" method="post">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <h2>Ajouter un calendrier</h2>
                <br>

                <input type="hidden" name="owner" value="{{ Auth::user()->id }}" readonly>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                            <span class="input-group-text" id="titre">Titre&nbsp;<span
                                    style="color: red">*</span></span>
                    </div>
                    <input type="text" class="form-control" placeholder="Titre événement" aria-label="titre"
                           value="" aria-describedby="titre" name="title" required>
                </div>


                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="titre">Couleur</span>
                    </div>
                    <input type="color" class="form-control" placeholder="Couleur" aria-label="color"
                           value="#e66465" aria-describedby="color" name="color">
                </div>

                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Logo</label>
                    </div>
                    <select class="custom-select .fa" id="inputGroupSelect01" name="logo">
                        <optgroup label="Medical Icons">
                            <option value="fas fa-pills">&#xf484; Pillules</option>
                            <option value="fas fa-user-md">&#xf0f0; Médecin</option>
                            <option value="fas fa-paw">&#xf1b0; Animal</option>
                        </optgroup>
                        <optgroup label="Party Icons">
                            <option value="fas fa-birthday-cake">&#xf1fd; Gateau</option>
                            <option value="fas fa-user-friends">&#xf500; Rendez-vous</option>
                            <option value="fas fa-utensils">&#xf2e7; Dinner</option>
                            <option value="fas fa-plane">&#xf072; Vacances</option>
                        </optgroup>
                        <optgroup label="Business Icons">
                            <option value="fas fa-money-bill-wave">&#xf53a; Jour de paye</option>
                            <option value="fas fa-wallet">&#xf555; Rendez-vous</option>
                        </optgroup>
                        <optgroup label="Other Icons">
                            <option value="fas fa-home">&#xf015; Home</option>
                            <option value="fas fa-futbol">&#xf1e3; Sport</option>
                            <option value="fas fa-code">&#xf121; Travail</option>
                            <option value="fas fa-couch">&#xf4b8; Série</option>
                            <option value="fas fa-comment-dots">&#xf4ad; Rendez-vous</option>
                        </optgroup>
                    </select>
                </div>

                <div id="submitdiv">
                    <button class="btn btn-primary info"
                            style="background-color: var(--secondary-color);border-color: var(--secondary-color)">
						<span>
							<i class="fas fa-calendar-alt"></i>
                            <span style="padding-left: 10px">Ajouter calendrier</span>
						</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
