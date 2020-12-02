@extends('layouts.header')

@section('content')

    <div class="container">
        <h1>Voici les notifications</h1>
        <?php $message = session()->get('message');
        if (!empty($message)){?>
        <div class="alert alert-dismissible" id="infoalert">
            <?php if ($message == "accept"){?>
            <script>alertmsg("infoalert", "success", "L'invitation a bien été accepté")</script>
            <?php
            }elseif ($message == "refuse"){?>
            <script>alertmsg("infoalert", "info", "L'invitation a bien été refusé")</script>
            <?php
            }?>
        </div>
        <?php
        }?>


        <table class="table table-bordered table-hover w-100">
            <thead>
            <tr class="d-flex">
                <th class="col-6">Nom du Calendrier</th>
                <th class="col-5">Auteur</th>
                <th class="col-1">Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php
            for ($i = 0; $i < count($invitation); $i++) {
            ?>
            <tr class="d-flex">
                <td class="col-sm-6"><?= $invitation[$i]['title']?></td>
                <td class="col-sm-5"><?= $invitation[$i]['name'] ?></td>

                <td class="col-sm-1">
                    <form action="{{ action('NotificationController@accept') }}" method="post">
                        <input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'>
                        <input type='hidden' name='idCalendar' value='<?= $invitation[$i]['id']?>'>
                        <button type='submit' class='btn btn-success'><i class='fas fa-check'></i></button>
                    </form>

                    <form action="{{ action('NotificationController@refuse') }}" method="post">
                        <input type='hidden' name='_token' value='<?php echo csrf_token(); ?>'>
                        <input type='hidden' name='idCalendar' value='<?= $invitation[$i]['id']?>'>
                        <button type='submit' class='btn btn-danger'><i class='fas fa-times'></i></button>
                    </form>
                </td>
            </tr>

            <?php
            }
            ?>
            </tbody>
        </table>
    </div>

    <script>

    </script>
@endsection
