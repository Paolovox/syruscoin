@extends('layouts.default')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

<style>
.card{
  width: 22%;
}

</style>

<div class="row" style="width:100%;margin-top:3%">

  <div class="form-group" style="width:70%; margin:0 auto">
    <label class="sr-only" for="exampleInputAmount">Tx Hash,Address</label>
    <div class="input-group">
      <div class="input-group-addon addon-cerca">Transazione</div>
      <input type="text" disabled class="form-control search" id="search" placeholder="<?= $txid ?>">
    </div>
  </div>

</div>


<div class="row" style="margin-top:3%;width:100%">

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">#Blocco</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title"><?= $block ?></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Miner</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">Data</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title"><?= $time ?></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Stato</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title"><?= $state ?></h5>
      </div>
    </div>

    <table class="transazioni table table-hover table-bordered" style="margin-top:2%;width:100%">
    <tbody>
      <tr>
        <td style="text-align:center"><?= $address_from ?>   <i class="fa fa-arrow-right fa-lg" style="color:#00ff00;padding-left:2%;padding-right:2%" aria-hidden="true"></i>   <?= $address_to ?>   <span style="padding-left:2%;color: #f8b214"> <?= $coins ?> SYC</span></td>
      </tr>

    </tbody>
  </table>

</div>

@stop
