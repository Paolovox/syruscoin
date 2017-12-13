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
                      <input name="token" type="text" class="form-control" id="token">
                    </div>
                    <div class="col-sm-3">
                      <div class="text-center">
                          <button type="submit" class="btn btn-default">Get Info</button>
                      </div>
                    </div>
                  </div>

                  <!-- INFO -->
                  <div style="display:" class="col-md-12 form-horizontal form-group">
                    <h5 style="text-align:center;display:inherit !important">INFO</h5>

                    <div class="col-md-3">
                      Username<br>
                      <strong><?= $name ?></strong>
                    </div>
                    <div class="col-md-8">
                      Wallet<br>
                      <strong><?= $wallet ?></strong>
                    </div>
                    <div class="col-md-1">
                      Coin<br>
                      <strong><?= $balance ?></strong>
                    </div>
                  </div>
                  <!-- END INFO -->

                </form>

                <br>
            </div>

        </div>
    </div>


  </div>
</div>


@stop
