@extends('layouts.default')
@section('content')
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>


<div class="content-area home-area-1 recent-property" style="margin-top:1%;background-color: #FCFCFC; padding-bottom: 55px;">
  <div class="container">

    <div class="col-md-6 col-md-offset-3">
        <div class="box-for overflow">
            <div class="col-md-12 col-xs-12 login-blocks">
                <h2 style="text-align:center;display:inherit !important">Login  </h2>
                <form action="/api/login" method="post">
                    <div class="form-group">
                        <label for="email">Username</label>
                        <input name="username" type="text" class="form-control" id="username">
                    </div>
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input name="password" type="password" class="form-control" id="password">
                    </div>
                    <div class="text-center">
                        <button type="submit" class="btn btn-default"> Log in</button>
                    </div>
                </form>
                <br>

                <h2 style="text-align:center;display:inherit !important">Social login  </h2>

                <p>
                <a class="login-social" href="#"><i class="fa fa-facebook"></i>&nbsp;Facebook</a>
                <a class="login-social" href="#"><i class="fa fa-google-plus"></i>&nbsp;Gmail</a>
                <a class="login-social" href="#"><i class="fa fa-twitter"></i>&nbsp;Twitter</a>
                </p>
            </div>

        </div>
    </div>


  </div>
</div>


@stop
