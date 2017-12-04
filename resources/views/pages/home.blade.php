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

</style>


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
                    <tr>
                      <td>57bff79a1a021bd2034f8fb97a42ba9c6f687f81b16c6d2a4730eca932cafad3</td>
                      <td>2.5 SYC</td>
                      <td>Dec 4, 2017 4:31:00 AM</td>
                    </tr>
                    <tr>
                      <td>aabff79a1aabr982034f8f123442ba9c6f687f81b16c6d2a4730eca932aht479</td>
                      <td>0.1 SYC</td>
                      <td>Dec 4, 2017 4:32:23 AM</td>
                    </tr>
                    <tr>
                      <td>99raf79a1a022342034f8f123442ba9cvree48ass486c6d2a4730eca93a345tgr</td>
                      <td>1.5 SYC</td>
                      <td>Dec 4, 2017 4:35:20 AM</td>
                    </tr>
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
