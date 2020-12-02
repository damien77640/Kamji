<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>
    <link rel="shortcut icon" href="{{ asset('favicon.ico') }}">
    <link rel="icon" type="image/png" href="{{ asset('favicon-32x32.png') }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <script src="{{ asset('js/script.js') }}"></script>
    <script src="http://code.jquery.com/jquery-3.3.1.min.js"
            integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
            crossorigin="anonymous">
    </script>
    <script src="{{ asset('js/JqueryQRCode/jquery.qrcode.js') }}" defer></script>
    <script src="{{ asset('js/JqueryQRCode/qrcode.js') }}" defer></script>

    <!-- FullCalendar -->

    <link href="{{ asset('js/fullcalendar/core/main.css') }}" rel="stylesheet">
    <link href="{{ asset('js/fullcalendar/daygrid/main.css') }}" rel="stylesheet">
    <link href="{{ asset('js/fullcalendar/timegrid/main.css') }}" rel="stylesheet">
    <link href="{{ asset('js/fullcalendar/list/main.css') }}" rel="stylesheet">
    <script src="{{ asset('js/fullcalendar/core/main.js') }}" defer></script>
    <script src="{{ asset('js/fullcalendar/interaction/main.js') }}" defer></script>
    <script src="{{ asset('js/fullcalendar/daygrid/main.js') }}" defer></script>
    <script src="{{ asset('js/fullcalendar/timegrid/main.js') }}" defer></script>
    <script src="{{ asset('js/fullcalendar/list/main.js') }}" defer></script>
    <script src="{{ asset('js/fullcalendar/core/locales/fr.js') }}" defer></script>


    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Icon picker -->
    <link href="{{ asset('css/fontawesome-5.11.2/css/all.css') }}" rel="stylesheet">
    <link href="{{ asset('css/fontawesome-5.11.2/js/all.js') }}" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="{{ asset('css/main.css') }}" rel="stylesheet">
    <link href="{{ asset('css/slider.css') }}" rel="stylesheet">

    <style type="text/css">
        body {
            --primary-color: #ffffff;
            --secondary-color: #1abc9c;
            --thirdary-color: #2c3e50;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar Holder -->
    <nav id="sidebar" class="active">
        <ul class="list-unstyled components">
            <?php
            $firstcalendar = DB::table('calendar')->where('owner', [Auth::user()->id] ?? '')->get();
            $idCalendar = session()->get('idCalendar');
            if (!isset($idCalendar)) {
                $idCalendar = $firstcalendar[0]->id;
            }

            $iduseractif = [Auth::user()->id][0];
            //regarde tout les calendrier
            //on crée un tableau avec la liste des id ou on est dans la liste des confirmer
            $shareCalendarlist = [];
            $calendarid = "";
            $resQuery = DB::table('calendar')
                ->where('participant_conf', 'like', "%{$iduseractif}%")
                ->get();
            foreach ($resQuery as $calendar) {
                $arr = [];
                foreach ($calendar as $key => $value) {
                    if ($key == "participant_conf") {
                        $arrayuser = explode(",", str_replace(array('{', '}'), '', $value));
                        if (in_array($iduseractif, $arrayuser)) {
                            $flag = true;
                        }
                    }
                    $arr[$key] = $value;

                }
                if (sizeof($arr) != 0) {
                    array_push($shareCalendarlist, $calendar->id);
                }
            }
            //on reparcour la iste des calendrier avec les 2 conditions
            $results = DB::table('calendar')->get();
            foreach ($results as $result) {
            if (($result->owner == $iduseractif) || in_array($result->id, $shareCalendarlist)){
            if ($idCalendar == $result->id) {
                $calendartitle = $result->title;
                $calendarid = $result->id;
                echo '<li class="active">';
            } else {
                echo '<li>';
            }

            ?>
            <form action="{{ action('HomeController@changeCalendar') }}" method="post" class="form-slider">
                <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                <input type="hidden" name="idCalendar" value="{{ $result->id }}">
                <?php
                if ($result->id == $idCalendar){?>
                <button type="submit" class="btn btn-slider"
                        style="background-color:<?=$result->color?>;border:16px solid rgba(0, 0, 0, 0.25);">
                    <?php
                    }else{ ?>
                    <button type="submit" class="btn btn-slider" style="background-color:<?=$result->color?>">
                        <?php
                        } ?>
                        <i style="text-align:center" class="icon-side center-icon <?=$result->logo?>"></i>
                    </button>
            </form>
            </li>
            <?php
            }
            }
            ?>
            <li id="createbutton">
                <a href="{{ route('newCalendar') }}" class="aslide"
                   style="color: rgba(0, 0, 0, 0.25);display: flex;align-items: center;justify-content: center;">
                    <i style="text-align:center;" class="center-icon icon-side fas fa-plus icon-side icon-size"></i>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Page Content Holder -->
    <div id="content">

        <nav class="navbar navbar-expand-md navbar-light shadow-sm" style="background-color: #2c3e50">
            <div class="container-fluid">

                <button type="button" id="sidebarCollapse" class="navbar-btn active" style="background-color: #2c3e50">
                    <span class="bg-white"></span>
                    <span class="bg-white"></span>
                    <span class="bg-white"></span>
                </button>


                <div style="text-align: center;">
                    <a class="" href="{{ url('/') }} "
                       style="color: white;font-size: 42px;font-weight: bold">
                        {{ config('app.name', 'Laravel') }}
                        - <?php echo($calendartitle); if (in_array($calendarid, $shareCalendarlist))
                            print(' <i class="fas fa-share-alt"></i>');?>
                    </a>
                </div>

                <div id="navbarSupportedContent">
                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ml-auto" style="font-size: 24px;font-weight: bold">
                        <!-- Authentication Links -->
                        @guest
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}"
                                   style="color:var(--secondary-color);font-size: 30px">{{ __('Connexion') }}</a>
                            </li>
                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}"
                                       style="color:var(--secondary-color);font-size: 30px">{{ __("S'inscrire") }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <!-- actual user : Auth::user()->name don't forget the "{" -->
                                <a id="navbarDropdown" class="nav-link" href="#" role="button"
                                   data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre
                                   style="font-size: 30px"
                                   onclick="addClassByClick()">
                                    <i class="fas fa-user text-white"></i>
                                </a>


                                <div class="dropdown-menu dropdown-menu-right add_here"
                                     aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('home') }}">
                                        {{ __('Home') }}
                                    </a>
                                    <a class="dropdown-item" href="{{ route('notification') }}">
                                        {{ __('Invitation') }}
                                        <?php if($nbInvitation > 0){ ?>
                                        <span class="badge badge-info">
                                            <?= $nbInvitation ?>
                                        </span>
                                        <?php } ?>
                                    </a>
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                          style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="py-4">
        @yield('content')

        <!-- jQuery CDN - Slim version (=without AJAX) -->
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
                    integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo"
                    crossorigin="anonymous"></script>
            <!-- Popper.JS -->
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.0/umd/popper.min.js"
                    integrity="sha384-cs/chFZiN24E4KMATLdqdvsezGxaGsi4hLGOzlXwp5UZB1LY//20VyM2taTB4QvJ"
                    crossorigin="anonymous"></script>

            <script type="text/javascript">
                $(document).ready(function () {
                    $('#sidebarCollapse').on('click', function () {
                        $('#sidebar').toggleClass('active');
                        $(this).toggleClass('active');
                    });
                });
            </script>
        </main>
    </div>
</div>
</body>
</html>
