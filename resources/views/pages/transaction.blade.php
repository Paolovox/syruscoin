@extends('layouts.default')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<?php
$transaction = \App\Transaction::withTrashed()->find($_GET['id']);
 ?>

<div class="content-area home-area-1 recent-property" style="background-color: #FCFCFC; padding-bottom: 55px;">
    <div class="container">
        <div class="row">
          <div class="col-md-8">
            <h3>Transazione</h3>

            <form class="form-horizontal">
              <div class="form-group">
                <label for="inputEmail3" class="col-sm-2 control-label">HASH</label>
                <div class="col-sm-10">
                  <input type="text" value="<?= $transaction->hash ?>" disabled >
                </div>
              </div>

            </form>
          </div>
        </div>


        <div class="row">
          <div class="col-md-8">
            <h3>Sommario</h3>



          </div>
        </div>

  </div>
</div>


@stop
