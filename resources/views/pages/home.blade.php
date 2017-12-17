@extends('layouts.default')
@section('content')

<style>
 .search{
   background-color: #293641;
   border: 2px solid #35485a;
 }
 .search:focus{
   background-color: #293641;
   color:white;
 }
 .addon-cerca{
   background-image:none;
   border: 2px solid #35485a;
   background-color: #293641;
 }
 .card{
   background-color: #124050 !important;
   border-radius: 2% !important;
   border: 0;
   margin-left: 3%;
   width: 13%;
   text-align: center;
 }
 .card-title{
   font-weight: bold !important;
    margin-bottom: 0;
 }
 h5{
   font-size: 13px;
 }

 table th{
   color:white;
   background-color: #293641;
   border: 2px solid #35485a;

 }
 .table-striped > tbody > tr:nth-child(2n) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
   background-color: #232322;
}

table {
    font-weight: 300;
    padding: 0;
    margin: 0;
    font-size: 14px;
    line-height: 18px;
    position: relative;
}

table {
    border-collapse: collapse;
}

tr:nth-child(n + 8) {
    display: none;
}

</style>

<div class="row" style="width:100%;margin-top:3%">

  <div class="form-group" style="width:70%; margin:0 auto">
    <label class="sr-only" for="exampleInputAmount">Tx Hash,Address</label>
    <div class="input-group">
      <input type="text" class="form-control search" id="exampleInputAmount" placeholder="Tx Hash, Address">
      <div style="cursor:pointer" class="input-group-addon addon-cerca"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Cerca</div>
    </div>
  </div>

</div>


<div class="row" style="margin-top:3%;width:100%">

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">Blocco Corrente</h5>
        <h5 style="text-align:center" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Miners Attivi</h5>
        <h5 style="text-align:center" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">Transazioni</h5>
        <h5 style="text-align:center" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Reward Blocco</h5>
        <h5 style="text-align:center" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">Difficoltà</h5>
        <h5 style="text-align:center" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Tassa</h5>
        <h5 style="text-align:center" class="card-title"></h5>
      </div>
    </div>

</div>


<div class="row" style="margin-top:3%;width:100%;margin-left:2%">

  <h1 style="color:white"><i class="fa fa-cubes" aria-hidden="true"></i> Recenti Transazioni</h1> &nbsp;&nbsp; <h4 style="margin-top:1.2%"> le transazioni più recenti avvenute nel Network Syrus</h4>


  <table class="table table-hover table-striped table-bordered" style="margin-top:2%;width:94%">
  <thead>
    <tr>
      <th scope="col">Hash Transazione</th>
      <th scope="col">SyrusCoin</th>
      <th scope="col">Address</th>
      <th scope="col">Dimensione</th>
      <th scope="col">Data</th>
    </tr>
  </thead>
  <tbody>
    <tr class="table-active">
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
    </tr>
    <tr class="table-active">
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
    </tr>
    <tr class="table-active">
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
    </tr>
    <tr class="table-active">
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
    </tr>
    <tr class="table-active">
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
    </tr>
    <tr class="table-active">
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
      <td>Column content</td>
    </tr>
  </tbody>
</table>
</div>

@stop

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
