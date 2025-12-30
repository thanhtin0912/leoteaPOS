<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Bigdeal admin is super flexible, powerful, clean &amp; modern responsive bootstrap 4 admin template with unlimited possibilities.">
    <meta name="keywords" content="admin template, Bigdeal admin template, dashboard template, flat admin template, responsive admin template, web app">
    <meta name="author" content="pixelstrap">
    <title>Leotea - Premium Admin Template</title>
    <link href="<?= PATH_URL . 'assets/images/admin/' ?>favicon.ico" type="image/x-icon" rel="icon"/>
    <link href="<?= PATH_URL . 'assets/images/admin/' ?>favicon.ico" type="image/x-icon" rel="shortcut icon"/>
        <!-- slick icon-->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL . 'assets/css/login/' ?>slick.css">
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL . 'assets/css/login/' ?>slick-theme.css">
    <!-- Bootstrap css-->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL . 'assets/css/login/' ?>bootstrap.css">

    <!-- App css-->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL . 'assets/css/login/' ?>admin.css">


    <script type="text/javascript">
        var root = '<?=PATH_URL_ADMIN?>';
        var token_value = '<?=$this->security->get_csrf_hash()?>';
    </script>
    <script type="text/javascript" src="<?= PATH_URL . 'assets/js/jquery-1.11.2.min.js' ?>"></script>
    <script type="text/javascript" src="<?= PATH_URL . 'assets/js/admin/login.js' ?>"></script>
    <title>Dashboard</title>
</head>
<body>

<!-- page-wrapper Start-->
<div class="page-wrapper">
    <div class="authentication-box">
        <div class="container">
            <div class="row">
                <div class="col-md-5 p-0 card-left">
                    <div class="card bg-primary">
                        <div class="svg-icon">
                            <img class="center-block" src="<?= PATH_URL?>assets/images/logo.png">
                        </div>

                        <div class="single-item">
                            <div>
                                <div>
                                    <h3>Welcome to LEOTEA ADMIN</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-7 p-0 card-right">
                    <div class="card tab2-card">
                        <div class="card-body">
                            <ul class="nav nav-tabs nav-material" id="top-tab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="top-profile-tab" data-bs-toggle="tab" href="#top-profile" role="tab" aria-controls="top-profile" aria-selected="true"><span class="icon-user me-2"></span>Login</a>
                                </li>
                            </ul>
                            <div class="tab-content">
                                <div class="tab-pane fade show active">
                                    <form class="form-horizontal auth-form">
                                        <div class="form-group">
                                            <input onkeypress="return EnterLogin(event)" required="" type="text" class="form-control" placeholder="Username" id="loginUser">
                                        </div>
                                        <div class="form-group">
                                            <input onkeypress="return EnterLogin(event)" required="" type="password" class="form-control" placeholder="Password" id="loginPass">
                                        </div>
                                        <div class="form-button">
                                            <button class="btn btn-primary" type="submit" onclick="login()">Login</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


</body>
</html>