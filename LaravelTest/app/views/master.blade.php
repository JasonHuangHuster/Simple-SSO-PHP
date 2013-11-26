<!DOCTYPE html>

<html>

<head>
    <title>电商部门SSO</title>
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
        {{ HTML::style('css/style.css') }}
        {{ HTML::style('css/bootstrap.css') }}
        {{ HTML::style('css/bootstrap-responsive.css') }}
</head>

<body>
    <div class="navbar navbar-inverse navbar-fixed-top">

        <div class="navbar-inner">
            <div class="container">
                <a href="#" class="brand active">SSO登录站点</a>

                <ul class="nav pull-right">
                    <li>
                        <a href="#" role="button" class="dropdown-toggle" data-toggle="dropdown">Dropdown 3 <b class="
                       </li>
                       caret"></b></a>
                        <ul class="dropdown-menu">
                            <li> <a href="#">Music</a>
                            </li>
                            <li> <a href="#">Pic</a>
                            </li>
                            <li> <a href="#">Video</a>
                            </li>
                        </ul>
                </ul>
            </div>
        </div>
    </div>

@section('content')
@show
    

    </div>
    <div class="alert alert-error">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <h2>
            Warning!
        </h2>
        This is really awesome !! check it out ~~
    </div>





    <div class='navbar navbar-inverse navbar-fixed-bottom'>
        <div class='navbar-inner'>
            <div class="container">
                <p href = "www.oppo.com" class="navbar-text pull-left">www.oppo.com</p>
            </div>
        </div>
    </div>



   @section('script')
        <!-- Scripts are placed here -->
        {{ HTML::script('js/jquery-1.9.1.js') }}
        {{ HTML::script('js/bootstrap.js') }}
        {{ HTML::script('js/systemTools.js') }}
        {{ HTML::script('js/checkInput.js') }}
        {{ HTML::script('js/highcharts.js') }}
        {{ HTML::script('js/modules/exporting.js') }}
        @show
    


</body>

</html>
