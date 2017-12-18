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
   text-align: center;

 }
 .table-striped > tbody > tr:nth-child(2n) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
}

table {
    font-weight: 300;
    padding: 0;
    margin: 0;
    font-size: 10px;
    line-height: 18px;
    position: relative;
}

table {
    border-collapse: collapse;
}

tr:nth-child(n + 8) {
    display: none;
}

table tr{
  font-weight: bold;
  color: #c0ecfe;
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
        <h5 style="text-align:center;color:#76e5a9" class="card-title current_block"></h5>
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

  <h1 style="color:white"><i class="fa fa-cubes" aria-hidden="true"></i> Transazioni Recenti</h1> &nbsp;&nbsp; <h4 style="margin-top:1.2%"> le transazioni più recenti avvenute nel Network Syrus</h4>


  <table class="transazioni table table-hover table-bordered" style="margin-top:2%;width:94%">
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
    <!-- <tr class="table-active">
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
    </tr> -->
  </tbody>
</table>
</div>

@stop

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<script>
function dissolvi(id){
  $('#'+id).animate({
      'opacity': '0.5'
  }, 2000, function () {
      $('#'+id).css({
          'backgroundColor': 'rgba(255,255,255,0.075)',
          'opacity': '1'
      });
  });
}

$(document).ready(function(){
    currentBlock();


  $.get('/getLastTransactions',{},function(data){
    $.each(data, function(i,e){
      var address_to = e.address;
      var hash = i;
      var qty = e.coins;
      var data = e.time;

      var row="<tr class='table-active' id="+i+"><td><a style='color:#76e5a9;text-decoration: underline' href=''>"+hash+"</a></td><td>"+qty+" SYC</td><td>"+address_to+"</td><td></td><td>"+data+"</td></tr>"
      $(".transazioni tbody").prepend(row);
    })
  })

  window.setInterval(function() {

      $.get('/getLastTransactions',{},function(data){

          $.each(data, function(i,e){

            var exists = false;

            $('.transazioni tbody tr').each(function(id,etr){
              var row = $(this);
              var row_id = row.prop('id');

              if(row_id == i){
                exists = true;
              }

            })

            if(exists == false){
              var address_to = e.address;
              var hash = i;
              var qty = e.coins;
              var data = e.time;

              var row="<tr class='table-active' id="+i+"> \
              <td><a style='color:#76e5a9;text-decoration: underline' href=''>"+hash+"</a></td> \
              <td>"+qty+" SYC</td> \
              <td>"+address_to+"</td> \
              <td></td> \
              <td>"+data+"</td></tr>"
              $(".transazioni tbody").prepend(row);
              $('#'+i).css('backgroundColor', '#b5bd68');
              dissolvi(i);
            }


            if($('.transazioni tbody tr').length == 0){

              $.each(data, function(i,e){
                var address_to = e.address;
                var hash = i;
                var qty = e.coins;
                var data = e.time;

                var row="<tr class='table-active' id="+i+"><td><a style='color:#76e5a9;text-decoration: underline' href=''>"+hash+"</a></td><td>"+qty+" SYC</td><td>"+address_to+"</td><td></td><td>"+data+"</td></tr>"
                $(".transazioni tbody").prepend(row);
                $('#'+i).css('backgroundColor', '#b5bd68');
                dissolvi(i);
              })
            }

          })

      });
  },10000);




  window.setInterval(function() {
      currentBlock();
  },10000);


});


function currentBlock(){
  $.get('/getCurrentBlock',{},function(data){
    data = JSON.parse(data);
    var current_block = data.current_block;
    $('.current_block').html(current_block);
  });
}

</script>
