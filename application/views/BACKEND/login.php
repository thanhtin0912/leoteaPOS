<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <link href="<?= PATH_URL . 'assets/images/admin/' ?>favicon.ico" type="image/x-icon" rel="icon"/>
    <link href="<?= PATH_URL . 'assets/images/admin/' ?>favicon.ico" type="image/x-icon" rel="shortcut icon"/>
    <link rel="stylesheet" href="<?= PATH_URL . 'assets/css/admin/login.css' ?>" type="text/css">
    <script type="text/javascript">
        var root = '<?=PATH_URL_ADMIN?>';
        var token_value = '<?=$this->security->get_csrf_hash()?>';
    </script>
    <script type="text/javascript" src="<?= PATH_URL . 'assets/js/jquery-1.11.2.min.js' ?>"></script>
    <script type="text/javascript" src="<?= PATH_URL . 'assets/js/admin/login.js' ?>"></script>
    <title>Dashboard</title>
</head>
<body>
<div id="main">
    <div class="logo"></div>
    <div class="bg_login">
        <div class="divInpUsername"><input onkeypress="return EnterLogin(event)" class="inpLogin" type="text"
                                           id="loginUser" placeholder="Username"/></div>
        <div class="divInpPass"><input onkeypress="return EnterLogin(event)" class="inpLogin" type="password"
                                       id="loginPass" placeholder="Password"/></div>
        <div onclick="login()" class="btLogin"></div>
        <div id="divError"></div>
    </div>
</div>
</body>
</html>