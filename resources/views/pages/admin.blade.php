@extends('layouts.default')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<div class="content-area home-area-1 recent-property" style="margin-top:1%;background-color: #FCFCFC; padding-bottom: 55px;">
  <div class="container">

    <div class="col-md-8 col-md-offset-2">
        <div class="box-for overflow">
            <div class="col-md-12 col-xs-12 login-blocks">
                <h2 style="text-align:center;display:inherit !important">ADMIN PANEL  </h2>

                <form class="form-horizontal" method="post" action="/api/get-info">
                  <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Token</label>
                    <div class="col-sm-7">
                      <input name="token" type="text" class="form-control" id="token" value="<?= isset($info) ? $token : ''; ?>">
                    </div>
                    <div class="col-sm-3">
                      <div class="text-center">
                          <button type="submit" class="btn btn-default">Get Info</button>
                      </div>
                    </div>
                  </div>
                </form>

                  <!-- INFO -->
                  <?php if(isset($info)){ ?>
                    <div style="padding-bottom: 5%;background-color:aliceblue" class="col-md-12 form-horizontal form-group">
                      <h5 style="text-align:center;display:inherit !important"><strong>INFO</strong></h5>

                      <div class="col-md-3">
                        Username<br>
                        <strong><?= $name ?></strong>
                      </div>
                      <div class="col-md-7">
                        Wallet<br>
                        <strong><?= $wallet ?></strong>
                      </div>
                      <div class="col-md-2">
                        Coin<br>
                        <strong><?= $balance ?></strong>
                      </div>
                    </div>
                  <?php } ?>
                  <!-- END INFO -->

                <br>


                <form class="form-horizontal" method="post" action="/api/send-to">

                  <input type="hidden" name="token" value="<?= isset($info) ? $token : ''; ?>" id="token_send">

                  <div class="form-group">
                    <label for="" class="col-sm-2 control-label">Username</label>
                    <div class="col-sm-4">
                      <input name="username" type="text" class="form-control" id="username">
                    </div>

                    <label for="" class="col-sm-1 control-label">Coins</label>
                    <div class="col-sm-2">
                      <input name="coins" type="text" class="form-control" id="coins">
                    </div>

                    <div class="col-sm-3">
                      <div class="text-center">
                          <button type="submit" class="btn btn-default">Send Coins</button>
                      </div>
                    </div>
                  </div>
                </form>

            </div>

        </div>
    </div>


  </div>
</div>

<script>
  $(document).ready(function(){

    $('#token').change(function(){
      var token = $('#token').val();
      $('#token_send').val(token);
    })
  })

</script>

@stop
