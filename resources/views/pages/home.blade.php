@extends('layouts.default')
@section('content')

<style>

table {
    font-family: 'Open Sans', sans-serif;
    font-weight: 300;
    padding: 0;
    margin: 0;
    font-size: 14px;
    line-height: 28px;
    color: #777;
    background: #fff;
    position: relative;
}

table {
    border-collapse: collapse;
}

tr:nth-child(n + 8) {
    display: none;
}

</style>

<?php
$transactions = App\Transaction::whereNotNull("deleted_at")->orderBy('created_at', 'desc')->get();
 ?>

<div class="content-area home-area-1 recent-property" style="background-color: #FCFCFC; padding-bottom: 55px;">
    <div class="container">
        <div class="row">
            <div class="col-md-8 text-left page-title">
                <h2>Ultime Transazioni</h2>


                <table class="table table-striped table-hover" style="color:#323232">
                  <thead>
                    <tr>
                      <th>Hash</th>
                      <th>Valore</th>
                      <th>Data</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php foreach ($transactions as $tran) { var_dump($transactions); ?>
                      <tr>
                        <td><?= $tran->hash; ?></td>
                        <td><?= $tran->qty ?> SYC</td>
                        <td><?= $tran->created_at; ?></td>
                      </tr>
                    <?php } ?>
                  </tbody>
                </table>
            </div>


            <!-- NEWS -->
            <div class="col-md-4 text-center  wow fadeInRight text-left page-title">
                <h2>News</h2>
                <ul class="footer-blog">
                    <li>
                        <div class="col-md-3 col-sm-4 col-xs-4 blg-thumb p0">
                            <a href="single.html">
                                <img src="theme/assets/img/logo.png">
                            </a>
                            <span class="blg-date">04/12/2017</span>

                        </div>
                        <div class="col-md-8  col-sm-8 col-xs-8  blg-entry">
                            <h6> <a href="#">SyrusCoin </a></h6>
                            <p style="line-height: 17px; padding: 8px 2px;">E' nato il SyrusCoin. </p>
                        </div>
                    </li>


                </ul>
            </div>
        </div>

    </div>
</div>



@stop
