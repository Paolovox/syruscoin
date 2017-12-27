@extends('layouts.default')
@section('content')


<div class="row" style="width:100%;margin-top:3%">

  <div class="form-group" style="width:70%; margin:0 auto">
    <label class="sr-only" for="exampleInputAmount">Tx Hash,Address</label>
    <div class="input-group">
      <input type="text" class="form-control search" id="search" placeholder="Cerca per hash transazione o address">
      <div style="cursor:pointer" onclick="search()" class="input-group-addon addon-cerca"><i class="fa fa-search" aria-hidden="true"></i>&nbsp;Cerca</div>
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
        <h5 style="text-align:center;color:#76e5a9" class="card-title count_miners"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">Transazioni</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title count_transactions"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Reward Blocco</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;" >
      <div class="card-body">
        <h5 class="card-title">Difficoltà</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title current_difficulty"></h5>
      </div>
    </div>

    <div class="card text-white bg-primary mb-3" style="max-width: 20rem;">
      <div class="card-body">
        <h5 class="card-title">Tassa</h5>
        <h5 style="text-align:center;color:#76e5a9" class="card-title"></h5>
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

  </tbody>
</table>
</div>

@stop

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<script>


function search(){
  var term = $('#search').val();
  window.location.href = "/search?src="+term;
}

function dissolvi(id){
  $('#'+id).animate({
      'opacity': '0.5'
  }, 3000, function () {
      $('#'+id).css({
          'backgroundColor': 'rgba(255,255,255,0.075)',
          'color': '#9ee3ff',
          'opacity': '1'
      });
  });
}

$(document).ready(function(){
    currentBlock();
    currentDifficulty();
    listTransaction();
    countTransactions();
    countMiners();


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
              <td><a style='color:#76e5a9;text-decoration: underline' href='/transaction?tx="+hash+"'>"+hash+"</a></td> \
              <td style='text-align:center'>"+qty+" SYC</td> \
              <td>"+address_to+"</td> \
              <td style='text-align:center'>"+e.size+" bytes</td> \
              <td>"+data+"</td></tr>"
              $(".transazioni tbody").prepend(row);
              $('#'+i).css('backgroundColor', 'black');
              $('#'+i).css('color', 'red');

              dissolvi(i);
            }


            if($('.transazioni tbody tr').length == 0){

              $.each(data, function(i,e){
                var address_to = e.address;
                var hash = i;
                var qty = e.coins;
                var data = e.time;

                var row="<tr class='table-active' id="+i+"> \
                <td><a style='color:#76e5a9;text-decoration: underline' href='/transaction?tx="+hash+"'>"+hash+"</a></td> \
                <td style='text-align:center'>"+qty+" SYC</td> \
                <td>"+address_to+"</td> \
                <td style='text-align:center'>"+e.size+" bytes</td> \
                <td>"+data+"</td></tr>"
                $(".transazioni tbody").prepend(row);
                $('#'+i).css('backgroundColor', '#b5bd68');
                dissolvi(i);
              })
            }

          })

      });

      currentBlock();
      countMiners();
      currentDifficulty();
      countTransactions();
  //    randomTransactions();

},2000);




});


function currentBlock(){
  $.get('/getCurrentBlock',{},function(data){
    data = JSON.parse(data);
    var current_block = data.current_block;
    $('.current_block').html(current_block);
  });
}

function currentDifficulty(){
  $.get('/getCurrentDifficulty',{},function(data){
    data = JSON.parse(data);
    var current_difficulty = data.current_difficulty;
    $('.current_difficulty').html(current_difficulty);
  });
}

function listTransaction(){
  $.get('/getLastTransactions',{},function(data){
    $.each(data, function(i,e){
      var address_to = e.address;
      var hash = i;
      var qty = e.coins;
      var data = e.time;

      var row="<tr class='table-active' id="+i+"> \
      <td><a style='color:#76e5a9;text-decoration: underline' href='/transaction?tx="+hash+"'>"+hash+"</a></td> \
      <td style='text-align:center'>"+qty+" SYC</td> \
      <td>"+address_to+"</td> \
      <td style='text-align:center'>"+e.size+" bytes</td> \
      <td>"+data+"</td></tr>"
      $(".transazioni tbody").prepend(row);
    })
  })
}

function countTransactions(){
  $.get('/countTransactions',{},function(data){
    data = JSON.parse(data);
    var count = data.count;
    $('.count_transactions').html(count);
  })
}

function countMiners(){
  $.get('/countMiners',{},function(data){
    data = JSON.parse(data);
    var count = data.count;
    $('.count_miners').html(count);
  })
}


function randomTransactions(){ $.get('/random',{},function(data){}) }

</script>
