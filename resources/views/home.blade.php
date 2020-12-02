@extends('layouts.header')

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <script>
                let idEvent;
                document.addEventListener('DOMContentLoaded', function () {
                    var calendarEl = document.getElementById('calendar');

                    var calendar = new FullCalendar.Calendar(calendarEl, {
                        eventClick: function (info) {
                            scrolltop()
                            $.ajaxSetup({
                                headers: {
                                    'X-CSRF-TOKEN': $('input[name="csrf-token"]').attr('content')
                                }
                            });
                            $.ajax({
                                url: "{{ url('/ajaxEventInfo') }}",
                                method: 'post',
                                data: {
                                    "_token": "{{ csrf_token() }}",
                                    idEvent: info.event.id,
                                },
                                complete: function () {
                                    $('#deleteevent').show();
                                },
                                success: function (result) {
                                    $('#idEvent').val(result.success.id)
                                    $('#idEventD').val(result.success.id)
                                    $('#modifTitle').val(result.success.title)
                                    $('#modifTitleD').val(result.success.title)
                                    $('#modifDescription').val(result.success.description)
                                    $('#modifColor').val(result.success.color)
                                }
                            });
                            $("#modifEvent_modal").modal();
                        },
                        plugins: ['interaction', 'dayGrid', 'timeGrid', 'list'],
                        header: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
                        },
                        locale: 'fr',
                        eventLimit: true,
                        navLinks: true, // can click day/week names to navigate views

                        businessHours: true, // display business hours
                        editable: false,
                        nowIndicator: true,
                        events:
                        <?php
                        $firstcalendar = DB::table('calendar')->where('owner', [Auth::user()->id] ?? '')->get();
                        $idCalendar = session()->get('idCalendar');
                        if (!isset($idCalendar)) {
                            $idCalendar = $firstcalendar[0]->id;
                        }
                        $results = DB::table('events')->where('calendrier', $idCalendar)->get();
                        foreach ($results as $result) {
                            if ($result->allDay == 1) {
                                $result->allDay = 'true';
                            }
                        }
                        echo json_encode($results);
                        ?>
                    });

                    calendar.render();
                });
            </script>
            <?php
            $firstcalendar = DB::table('calendar')->where('id', $idCalendar ?? '')->get();
            ?>
            <style>
                /*a ne surtout pas supprimer (règle un bug des modal)*/
                .modal-backdrop {
                    display: none;
                }

                .modal {
                    background: rgba(0, 0, 0, 0.5);
                }

                #calendar {
                    max-width: 900px;
                    margin: 0 auto;
                }
            </style>
            <div class="col-12 col-md-8">
                <style>
                    #overlay {
                        position: fixed;
                        width: 100%;
                        height: 100%;
                        left: 0;
                        top: 0;
                        background: rgba(51, 51, 51, 0.7);
                        z-index: 10;
                    }
                </style>
                <?php
                $message = session()->get('message');
                if (!empty($message)){?>
                <div class="alert alert-dismissible" id="infoalert">
                    <?php if ($message == "calendarCreate"){?>
                    <script>alertmsg("infoalert", "success", "Le calendrier a été créée")</script>
                    <?php
                    }elseif ($message == "calendarEdit"){?>
                    <script>alertmsg("infoalert", "success", "Le calendrier a bien été modifié")</script>
                    <?php
                    }elseif ($message == "calendarDelete"){?>
                    <script>alertmsg("infoalert", "success", "Le calendrier a bien été supprimé")</script>
                    <?php
                    }elseif ($message == "calendarDelete_Error"){?>
                    <script>alertmsg("infoalert", "danger", "Il doit te rester au moins 1 calendrier !")</script>
                    <?php
                    }elseif ($message == "eventCreate"){?>
                    <script>alertmsg("infoalert", "success", "L'évènement \"{{ session()->get( 'title' ) }}\" a été ajouté")</script>
                    <?php
                    }elseif ($message == "shareCalendar"){?>
                    <script>alertmsg("infoalert", "success", "L'invitation de partage a bien été envoyé")</script>
                    <?php
                    }elseif ($message == "EventEdit"){?>
                    <script>alertmsg("infoalert", "success", "L'évènement \"{{ session()->get( 'title' ) }}\" a été modifié")</script>
                    <?php
                    }elseif ($message == "EventDelete"){?>
                    <script>alertmsg("infoalert", "success", "L'évènement \"{{ session()->get( 'title' ) }}\" a été supprimé")</script>
                    <?php
                    }elseif ($message == "calendarLeft"){?>
                    <script>alertmsg("infoalert", "success", "Le calendrier a bien été quitté")</script>
                    <?php
                    }elseif ($message == "participantRemove"){?>
                    <script>alertmsg("infoalert", "success", "Le participant a bien été exclu")</script>
                    <?php
                    }elseif ($message == "QRCalendar"){?>
                    <script>alertmsg("infoalert", "success", "Vous avez rejoint un calendrier par QRCode")</script>
                    <?php
                    }?>
                </div>
                <?php
                }?>

                <div id='calendar'></div>
                <br>

                <div id="popoup" class="card" style="display:none">
                    <div class="card-header">Dashboard</div>
                    <input type="text" class="form-control" aria-label="titre edit" id="titre_edit"
                           value="" aria-describedby="titre" name="title-edit" required>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        You are logged in! {{ Auth::user()->name }}

                    </div>

                </div>

            </div>
            <div class="col-6 col-md-4">
                <form class="contact100-form validate-form" action="{{ action('HomeController@insert') }}"
                      method="post">
                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                    <h2>Ajouter un évènement</h2>
                    <br>


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
                            <span class="input-group-text">Description</span>
                        </div>
                        <textarea class="form-control" aria-label="Description" name="description"
                                  style="min-height:37px" rows="1"></textarea>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon1">Date de début&nbsp;<span
                                    style="color: red">*</span></span>
                        </div>
                        <input type="date" class="form-control" placeholder="jj-mm-aaaa" aria-label="date"
                               aria-describedby="basic-addon1" name="start" value="" required>
                    </div>
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="basic-addon2">Heure</span>
                        </div>
                        <input type="time" class="form-control" placeholder="jj-mm-aaaa" aria-label="heure"
                               aria-describedby="basic-addon2" name="heureDeb" value="">
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="gridCheck" onclick="hideFlex()"
                                   name="check-day">
                            <label class="form-check-label unselectable" for="gridCheck">
                                Toute la journée
                            </label>
                        </div>
                    </div>
                    <script>
                        function hideFlex() {
                            let x = document.getElementById("dateFin");
                            if (x.style.display === "none") {
                                x.style.display = "block";
                            } else {
                                x.style.display = "none";
                            }
                        }
                    </script>


                    <div id="dateFin">
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">Date de fin</span>
                            </div>
                            <input type="date" class="form-control" placeholder="jj-mm-aaaa" aria-label="date"
                                   aria-describedby="basic-addon3" name="end" value="">
                        </div>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon2">Heure</span>
                            </div>
                            <input type="time" class="form-control" placeholder="jj-mm-aaaa" aria-label="heure"
                                   aria-describedby="basic-addon2" name="heureFin" value="">
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="titre">Couleur</span>
                        </div>
                        <input type="color" class="form-control" placeholder="Couleur" aria-label="color"
                               value="#e66465" aria-describedby="color" name="color">
                    </div>


                    <input type="hidden" name="owner" value=" {{ Auth::user()->id }}" readonly>
                    <input type="hidden" name="calendrier" value="{{ $idCalendar }}" readonly>


                    <button class="btn btn-primary info"
                            style="background-color: var(--secondary-color);border-color: var(--secondary-color)">
                        <i class="fas fa-calendar-alt pr-2"></i>
                        Ajouter event
                    </button>
                </form>
            </div>
            <div>
                <!-- Button trigger modal -->
                <?php
                $iduseractif = [Auth::user()->id][0];
                //regarde tout les calendrier
                //on crée un tableau avec la liste des id ou on est dans la liste des confirmer
                $shareCalendarlist = [];
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
                if (!in_array($idCalendar, $shareCalendarlist)){
                ?>
                <button type="button" id="shareCalendar" class="btn btn-info" data-toggle="modal"
                        data-target="#shareCalendar_modal" onclick="scrolltop()">
                    <i class="fas fa-users pr-2"></i>
                    Partager le calendrier
                </button>
                <button type="button" id="viewshareCalendar" class="btn btn-warning" data-toggle="modal"
                        data-target="#viewshareCalendar_modal" onclick="scrolltop()">
                    <i class="fas fa-eye pr-2"></i>
                    Voir liste utilisateur
                </button>
                <button type="button" id="modifCalendar" class="btn btn-secondary" data-toggle="modal"
                        data-target="#editCalendar_modal" onclick="scrolltop()">
                    <i class="fas fa-pen pr-2"></i>
                    Modifier le calendrier
                </button>
                <button type="button" id="confirmSuppr" class="btn btn-danger" data-toggle="modal"
                        data-target="#supprimerCalendar_modal" onclick="scrolltop()">
                    <i class="fas fa-trash-alt pr-2"></i>
                    Supprimer le calendrier
                </button>
                <button class="btn btn-info" id="show_qrcode">
                    <i class="fas fa-qrcode fa-lg"></i>
                </button>

                <script>
                    $(function () {
                        $('#show_qrcode').popover({
                            container: "body",
                            html: true,
                            content: function () {
                                let qrcode = "<img id='barcode' \n" +
                                    "            src=\"https://api.qrserver.com/v1/create-qr-code/?data=http://kamji.herokuapp.com/home/qrcode?numcal=<?=$idCalendar?>&size=150x150\" \n" +
                                    "            alt=\"QRCode\" \n" +
                                    "            title=\"QRCode\" \n" +
                                    "            width=\"150\" \n" +
                                    "            height=\"150\" />"
                                return '<div class="popover-message">' + qrcode + '</div>';
                            }
                        });


                    });
                </script>
                <?php
                }else{?>
                <button type="button" id="confirmleft" class="btn btn-danger" data-toggle="modal"
                        data-target="#leftCalendar_modal" onclick="scrolltop()">
                    <i class="fas fa-door-open pr-2"></i>
                    Quitter le calendrier
                </button>
            <?php
            }?>

            <!-- Modal Suppression calendrier -->
                <div class="modal fade" id="supprimerCalendar_modal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Confirmation de suppression</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Etes-vous sur de supprimer le calendrier ?
                                <form action="{{ action('HomeController@supprCalendar') }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="idCalendar" value="{{ $idCalendar }}" readonly>
                                    <input type="hidden" name="idUser" value="{{ Auth::user()->id }}" readonly>
                                    <button class="btn btn-danger float-right mt-4" type="submit">
                                        <i class="fas fa-trash-alt fa-lg  pr-2"></i>
                                        Valider la suppression
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal quitter calendrier -->
                <div class="modal fade" id="leftCalendar_modal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Confirmation de la sortie</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                Etes-vous sur de quitter le calendrier ?
                                <form action="{{ action('HomeController@leftCalendar') }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="idCalendar" value="{{ $idCalendar }}" readonly>
                                    <input type="hidden" name="idUser" value="{{ Auth::user()->id }}" readonly>
                                    <button class="btn btn-danger float-right mt-4" type="submit">
                                        <i class="fas fa-door-open fa-lg  pr-2"></i>
                                        Quitter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal View Partage calendrier -->

                <div class="modal fade" id="viewshareCalendar_modal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">
                                    Liste des participants confirmés</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body" id="confparticipant">

                                <script>
                                    function insertuserV(name, id) {

                                        let row = document.createElement("div");
                                        row.setAttribute('class', "row");

                                        let col1 = document.createElement("div");
                                        col1.setAttribute('class', "col");
                                        col1.innerText = name

                                        let col2 = document.createElement("div");
                                        col2.setAttribute('class', "col col-lg-2");

                                        let f = document.createElement("form");
                                        f.setAttribute('method', "post");
                                        f.setAttribute('action', "{{ action('HomeController@shareviewCalendar') }}");

                                        let i1 = document.createElement("input");
                                        i1.setAttribute('type', "hidden");
                                        i1.setAttribute('name', "_token");
                                        i1.setAttribute('value', "<?php echo csrf_token(); ?>");
                                        let i2 = document.createElement("input");
                                        i2.setAttribute('type', "hidden");
                                        i2.setAttribute('name', "idCalendar");
                                        i2.setAttribute('value', "{{ $idCalendar }}");
                                        let i3 = document.createElement("input");
                                        i3.setAttribute('type', "hidden");
                                        i3.setAttribute('name', "userConf");
                                        i3.setAttribute('value', id);

                                        let s = document.createElement("button");
                                        s.setAttribute('type', "submit");
                                        s.setAttribute('class', "btn btn-danger");

                                        s.innerHTML = '<i class="fas fa-trash-alt"></i>'


                                        row.appendChild(col1);
                                        row.appendChild(col2);

                                        col2.appendChild(f);

                                        f.appendChild(i1);
                                        f.appendChild(i2);
                                        f.appendChild(i3);
                                        f.appendChild(s);


                                        let hr = document.createElement("hr");
                                        let iddiv = document.getElementById('confparticipant')
                                        iddiv.appendChild(row);
                                        iddiv.appendChild(hr);

                                    }

                                    $(document).ready(function () {
                                        <?php $arrayuser = json_decode(json_encode($firstcalendar), true);
                                        $arrayuser = $arrayuser[0]["participant_conf"];
                                        $arrayuser = explode(",", str_replace(array('{', '}'), '', $arrayuser));
                                        foreach ($arrayuser as $value){
                                        if ( $value != ''){
                                        $idUser = DB::table('users')->where('id', $value)->first();
                                        $idUser = $idUser->email;
                                        ?>
                                        insertuserV('<?=$idUser?>', '<?=$value?>')
                                        <?php
                                        }
                                        }
                                        ?>
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Partage calendrier -->

                <div class="modal fade" id="shareCalendar_modal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Partage du calendrier</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="alert alert-warning alert-dismissible fade show" role="alert"
                                     id="alertshare" style="display: none">
                                </div>
                                A qui voulez-vous partagez ce calendrier?
                                <form action="{{ action('HomeController@shareCalendar') }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="idCalendar" value="{{ $idCalendar }}" readonly>
                                    <div class="row">
                                        <div class="col-10">
                                            <div class="input-group mb-3">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text" id="pseudo_share">Email</span>
                                                </div>
                                                <label for="user_list"></label>
                                                <input class="form-control" name="pseudonyme" id="user_list">
                                            </div>
                                        </div>
                                        <div class="col-2">
                                            <script>
                                                function insertuser(name, id) {
                                                    let tableRef = document.getElementById('tableUser').getElementsByTagName('tbody')[0];

                                                    // Insert a row in the table at the last row
                                                    let newRow = tableRef.insertRow();

                                                    // Insert a cell in the row at index 0
                                                    let newCell = newRow.insertCell(0);
                                                    let newCellbtn = newRow.insertCell(1);

                                                    // Append a text node to the cell
                                                    let newText = document.createTextNode(name);
                                                    newCellbtn.innerHTML = "<button type=\"button\"  class=\"btn btn-danger SupprUser\" onclick='removeuser(this)'>" +
                                                        "<i class=\"fas fa-trash-alt\"></i>" +
                                                        "</button>"
                                                    newCell.appendChild(newText);
                                                    if (document.getElementById("user_array").value === '')
                                                        document.getElementById("user_array").value = id
                                                    else
                                                        document.getElementById("user_array").value = document.getElementById("user_array").value + "," + id
                                                }

                                                function removeuser(element) {
                                                    let position = element.parentNode.parentNode.rowIndex
                                                    $(element).closest('tr').remove()
                                                    let user_array = document.getElementById("user_array").value
                                                    //si contient une "," = tableau
                                                    if (user_array.includes(",")) {
                                                        user_array = user_array.split(",")
                                                        let index = user_array.indexOf(user_array[position - 1]);
                                                        user_array.splice(index, 1);
                                                        document.getElementById("user_array").value = user_array.join()
                                                    } else {
                                                        document.getElementById("user_array").value = ""
                                                    }

                                                }
                                            </script>
                                            <button class="btn btn-secondary"
                                                    id="add_user"
                                                    type="button">
                                                <i class="fas fa-plus fa-lg"></i>
                                            </button>
                                            <button class="btn btn-secondary"
                                                    id="tempr"
                                                    type="button" style="display: none">
                                                <i class="fas fa-spinner fa-pulse fa-lg"></i>
                                            </button>
                                        </div>
                                    </div>


                                    <script>

                                        $(document).ready(function () {
                                            <?php $arrayuser = json_decode(json_encode($firstcalendar), true);
                                            $arrayuser = $arrayuser[0]["participant_wait"];
                                            $arrayuser = explode(",", str_replace(array('{', '}'), '', $arrayuser));
                                            foreach ($arrayuser as $value){
                                            if ( $value != ''){
                                            $idUser = DB::table('users')->where('id', $value)->first();
                                            $idUser = $idUser->email;
                                            ?>
                                            insertuser('<?=$idUser?>', '<?=$value?>')
                                            <?php
                                            }
                                            }
                                            ?>
                                            $('#add_user').click(function (e) {
                                                e.preventDefault();
                                                $.ajaxSetup({
                                                    headers: {
                                                        'X-CSRF-TOKEN': $('input[name="csrf-token"]').attr('content')
                                                    }
                                                });
                                                $.ajax({
                                                    url: "{{ url('/ajaxIDFromMail') }}",
                                                    method: 'post',
                                                    data: {
                                                        "_token": "{{ csrf_token() }}",
                                                        mail: $('#user_list').val(),
                                                        list: $('#user_array').val(),
                                                        id: {{ Auth::user()->id }},
                                                    },
                                                    beforeSend: function () {
                                                        $('#tempr').show();
                                                        $('#add_user').hide();
                                                    },
                                                    complete: function () {
                                                        $('#tempr').hide();
                                                        $('#add_user').show();
                                                    },
                                                    success: function (result) {
                                                        let alert = $('#alertshare');
                                                        alert.show()
                                                        let htmlString = '';
                                                        if (result.success === "none") {
                                                            htmlString = "<strong>Erreur!</strong> Aucun utilisateur ne semble correspondre."
                                                        } else if (result.success === "double") {
                                                            htmlString = "<strong>Erreur!</strong> L'utilisateur est déjà dans la liste des demandes."
                                                        } else if (result.success === "you") {
                                                            htmlString = "<strong>Erreur!</strong> Vous avez déjà accès au calendrier"
                                                        } else {
                                                            insertuser(document.getElementById('user_list').value, result.success)
                                                            $('#alertshare').hide();
                                                            document.getElementById('user_list').value = "";
                                                        }
                                                        alert.html(htmlString)
                                                    }
                                                });
                                            });
                                        });
                                    </script>

                                    <input name="pseudonymeArray" id="user_array" type="hidden" readonly>
                                    <table id="tableUser" class="table table-hover">
                                        <thead class="thead-dark">
                                        <tr>
                                            <th class="col-10">Liste des invitations en attentes</th>
                                            {{--pour supprimer la personne--}}
                                            <th class="col-2"></th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        </tbody>
                                    </table>

                                    <button class="btn btn-success float-right mt-4" type="submit">
                                        <i class="fas fa-check fa-lg pr-2"></i>
                                        Valider le partage
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Modifications calendrier -->

                <div class="modal fade" id="editCalendar_modal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-fixed" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Modifications du calendrier</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form action="{{ action('HomeController@modifCalendar') }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="idCalendar" value="{{ session()->get( 'idCalendar' ) }}"
                                           readonly>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                            <span class="input-group-text">Titre&nbsp;<span
                                    style="color: red">*</span></span>
                                        </div>
                                        <input type="text" id="title_modif" class="form-control"
                                               placeholder="Titre événement" aria-label="titre"
                                               value="" aria-describedby="titre" name="title" required>
                                    </div>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Couleur</span>
                                        </div>
                                        <input type="color" id="color_modif" class="form-control" placeholder="Couleur"
                                               aria-label="color"
                                               value="#e66465" aria-describedby="color" name="color">
                                    </div>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <label class="input-group-text" for="logo_modif">Logo</label>
                                        </div>
                                        <select class="custom-select .fa" id="logo_modif" name="logo">
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
                                    <button class="btn btn-success float-right mt-4" type="submit">
                                        <i class="fas fa-check fa-lg  pr-2"></i>
                                        Valider les modifications du calendrier
                                    </button>
                                </form>
                            </div>
                            <script>
                                let all_info_calendar =  <?= json_encode($firstcalendar) ?>;
                                document.getElementById("title_modif").value = all_info_calendar[0].title;
                                document.getElementById("color_modif").value = all_info_calendar[0].color;
                                document.getElementById("logo_modif").value = all_info_calendar[0].logo;
                            </script>
                        </div>
                    </div>
                </div>

                <!-- Modal Modifications évènement -->

                <div class="modal fade" id="modifEvent_modal" tabindex="-1" role="dialog"
                     aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                    <div class="modal-dialog modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLongTitle">Modifications de l'évènement</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <form class="contact100-form validate-form"
                                      action="{{ action('HomeController@modifEvent') }}" method="post">
                                    <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                    <input type="hidden" name="id" id="idEvent" value="">
                                    <input type="hidden" name="idCalendar" value="{{ session()->get( 'idCalendar' ) }}"
                                           readonly>
                                    <h2>Modifier un évènement</h2>
                                    <br>


                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="titre">Titre&nbsp;<span
                                                    style="color: red">*</span></span>
                                        </div>
                                        <input type="text" class="form-control" placeholder="Titre événement"
                                               aria-label="titre" name="eventTitle"
                                               value="" aria-describedby="titre" id="modifTitle" required>
                                    </div>

                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text">Description</span>
                                        </div>
                                        <textarea class="form-control" aria-label="Description" id="modifDescription"
                                                  name="eventDesc"
                                                  style="min-height:37px" rows="1"></textarea>
                                    </div>
                                    <div class="input-group mb-3">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text" id="titre">Couleur</span>
                                        </div>
                                        <input type="color" class="form-control" placeholder="Couleur"
                                               aria-label="color"
                                               value="#e66465" aria-describedby="color" name="color" id="modifColor">
                                    </div>
                                    <!-- Rajouter accès à la BD pour récupere les autre informations tel que les titres...  et completer le questionnaire-->
                                    <button class="btn btn-success success"
                                            style="background-color: var(--secondary-color);border-color: var(--secondary-color)"
                                            type="submit">
                                        <i class="fas fa-check pr-2"></i>
                                        <span style="padding-left: 10px">Valider les modifications de l'évènement</span>

                                    </button>
                                </form>
                                <div id="deleteevent" style="display: none">
                                    <form class="contact100-form validate-form"
                                          action="{{ action('HomeController@deleteEvent') }}" method="post">

                                        <input type="hidden" name="_token" value="<?php echo csrf_token(); ?>">
                                        <input type="hidden" name="id" id="idEventD" value="" readonly>
                                        <input type="hidden" name="idCalendar"
                                               value="{{ session()->get( 'idCalendar' ) }}">
                                        <input type="hidden" name="eventTitle" value="" id="modifTitleD" readonly>
                                        <button class="btn btn-danger"
                                                type="submit">
                                        <span>
                                            <i class="fas fa-trash-alt pr-2"></i>
                                            Supprimer l'évènement
                                        </span>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
