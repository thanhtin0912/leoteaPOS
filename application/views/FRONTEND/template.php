<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bigdeal - Multi-purpopse E-commerce Html Template</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="description" content="big-deal">
    <meta name="keywords" content="big-deal">
    <meta name="author" content="big-deal">
    <link rel="icon" href="<?= PATH_URL; ?>assets/images/logo.jpg" type="image/x-icon">
    <link rel="shortcut icon" href="<?= PATH_URL; ?>assets/images/logo.jpg" type="image/x-icon">

    <!--icon css-->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/font-awesome.css">
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/themify.css">

    <!--Slick slider css-->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/slick.css">
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/slick-theme.css">

    <!--Animate css-->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/animate.css">
    <!-- Bootstrap css -->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/bootstrap.css">

    <!-- Theme css -->
    <link rel="stylesheet" type="text/css" href="<?= PATH_URL; ?>assets/css/frontend/color14.css" media="screen"
        id="color">
    <script src="<?= PATH_URL; ?>assets/js/frontend/jquery-1.9.1.min.js"></script>
</head>

<body class="bg-light">
    <input type="hidden" value="<?= $this->security->get_csrf_hash() ?>" id="csrf_token" />
    <input type="hidden"
        value="<?php if ($this->session->userdata('userLogin')) { echo $this->session->userdata('userLogin')->phone; };?>"
        id="checkUserInfo" />
    <!-- loader start -->
    <div class="loader-wrapper">
        <div>
            <img src="<?= PATH_URL; ?>assets/images/giphy1.gif" alt="loader">
        </div>
    </div>
    <!-- loader end -->

    <!--header start-->
    <header id="stickyheader">
        <div class="mobile-fix-option"></div>
        <div class="top-header2">
            <div class="custom-container">
                <div class="row">
                    <div class="col-md-8 col-sm-12">
                        <div class="top-header-left">
                            <ul>
                                <li>
                                    <a href="javascript:void(0)"><i class="fa fa-phone"></i>Hotline:
                                        <?=$info[0]->phone ?></a>
                                </li>
                                <?php if ($this->session->userdata('userLogin')) {?>
                                <li>
                                    <a href="javascript:void(0)">CH:
                                        <?= $this->session->userdata('userLogin')->storeName; ?> 
                                        | <?= $this->session->userdata('userLogin')->in; ?> - <?= $this->session->userdata('userLogin')->out; ?> </a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-4 col-sm-12">
                        <div class="top-header-right">
                            <ul>
                                <?php if (!$this->session->userdata('userLogin')) {?>
                                <li onclick="openAccount()">
                                    <a href="javascript:void(0)"><i class="fa fa-user"></i> Đăng nhập</a>
                                </li>
                                <?php } else { ?>
                                <li onclick="logOut()">
                                    <a href="<?= PATH_URL; ?>logout"><i class="fa fa-user"></i> Đăng xuất</a>
                                </li>
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="searchbar-main header7">
            <div class="custom-container">
                <div class="row">
                    <div class="col-12">
                        <div class="header-contain">
                            <div class="logo-block">
                                <div class="brand-logo">
                                    <a href="<?= PATH_URL; ?>">
                                        <img src="<?= PATH_URL; ?>assets/images/logo.jpg" class="img-fluid  w-50"
                                            alt="logo">
                                    </a>
                                </div>
                            </div>
                            <div class="menu-block">
                                <nav id="main-nav">
                                    <div class="toggle-nav"><i class="fa fa-bars sidebar-bar"></i></div>
                                    <ul id="main-menu" class="sm pixelstrap sm-horizontal">
                                        <li>
                                            <div class="mobile-back text-right">Back<i class="fa fa-angle-right ps-2"
                                                    aria-hidden="true"></i></div>
                                        </li>
                                        <!--HOME-->
                                        <li>
                                            <a class="dark-menu-item" href="<?= PATH_URL; ?>home">Sản phẩm</a>
                                        </li>
                                        <!--HOME-END-->
                                        <!--SHOP-->
                                        <li>
                                            <a class="dark-menu-item" href="<?= PATH_URL; ?>trang-thai-don-hang">Đơn hàng</a>
                                        </li>
                                        <!--SHOP-END-->
                                        <!--SHOP-->
                                        <li>
                                            <a class="dark-menu-item" href="<?= PATH_URL; ?>lich-su-don-hang">Lịch sử</a>
                                        </li>
                                        <!--SHOP-END-->
                                    </ul>
                                </nav>
                            </div>
                            <div class="icon-block">
                                <ul class="theme-color icon-radius">
                                    <li class="mobile-search">
                                        <svg enable-background="new 0 0 512.002 512.002" viewBox="0 0 512.002 512.002"
                                            xmlns="http://www.w3.org/2000/svg">
                                            <g>
                                                <path
                                                    d="m495.594 416.408-134.086-134.095c14.685-27.49 22.492-58.333 22.492-90.312 0-50.518-19.461-98.217-54.8-134.31-35.283-36.036-82.45-56.505-132.808-57.636-1.46-.033-2.92-.054-4.392-.054-105.869 0-192 86.131-192 192s86.131 192 192 192c1.459 0 2.93-.021 4.377-.054 30.456-.68 59.739-8.444 85.936-22.436l134.085 134.075c10.57 10.584 24.634 16.414 39.601 16.414s29.031-5.83 39.589-16.403c10.584-10.577 16.413-24.639 16.413-39.597s-5.827-29.019-16.407-39.592zm-299.932-64.453c-1.211.027-2.441.046-3.662.046-88.224 0-160-71.776-160-160s71.776-160 160-160c1.229 0 2.449.019 3.671.046 86.2 1.935 156.329 73.69 156.329 159.954 0 86.274-70.133 158.029-156.338 159.954z" />
                                                <path
                                                    d="m192 320.001c-70.58 0-128-57.42-128-128s57.42-128 128-128 128 57.42 128 128-57.42 128-128 128z" />
                                            </g>
                                        </svg>
                                    </li>
                                    <li class="mobile-wishlist d-block d-sm-none">
                                        <div class="list-delivery-method d-flex">
                                            <div class="deliery-method-card d-flex active">
                                                <div class="deliery-method-card__image">
                                                    <a href="<?= PATH_URL; ?>"><img width="40px"src="<?= PATH_URL; ?>assets/images/giphy1.gif" alt=""></a>
                                                </div>
                                            </div>

                                        </div>
                                    </li>
                                    <li class="mobile-cart item-count" onclick="openCart()">
                                        <svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
                                            viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;"
                                            xml:space="preserve">
                                            <g>
                                                <g>
                                                    <path d="M443.209,442.24l-27.296-299.68c-0.736-8.256-7.648-14.56-15.936-14.56h-48V96c0-25.728-9.984-49.856-28.064-67.936
                                  C306.121,10.24,281.353,0,255.977,0c-52.928,0-96,43.072-96,96v32h-48c-8.288,0-15.2,6.304-15.936,14.56L68.809,442.208
                                  c-1.632,17.888,4.384,35.712,16.48,48.96S114.601,512,132.553,512h246.88c17.92,0,35.136-7.584,47.232-20.8
                                  C438.793,477.952,444.777,460.096,443.209,442.24z M319.977,128h-128V96c0-35.296,28.704-64,64-64
                                  c16.96,0,33.472,6.784,45.312,18.656C313.353,62.72,319.977,78.816,319.977,96V128z" />
                                                </g>
                                            </g>
                                        </svg>
                                        <div class="item-count-contain inverce" id="count-cart-product">
                                            <?=$countCart?>
                                        </div>
                                    </li>
                                </ul>
                                <div class="toggle-nav"><i class="fa fa-bars sidebar-bar"></i></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="searchbar-input">
                <div class="input-group">
                    <span class="input-group-text"><svg version="1.1" xmlns="http://www.w3.org/2000/svg"
                            xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="28.931px"
                            height="28.932px" viewBox="0 0 28.931 28.932"
                            style="enable-background:new 0 0 28.931 28.932;" xml:space="preserve">
                            <g>
                                <path
                                    d="M28.344,25.518l-6.114-6.115c1.486-2.067,2.303-4.537,2.303-7.137c0-3.275-1.275-6.355-3.594-8.672C18.625,1.278,15.543,0,12.266,0C8.99,0,5.909,1.275,3.593,3.594C1.277,5.909,0.001,8.99,0.001,12.266c0,3.276,1.275,6.356,3.592,8.674c2.316,2.316,5.396,3.594,8.673,3.594c2.599,0,5.067-0.813,7.136-2.303l6.114,6.115c0.392,0.391,0.902,0.586,1.414,0.586c0.513,0,1.024-0.195,1.414-0.586C29.125,27.564,29.125,26.299,28.344,25.518z M6.422,18.111c-1.562-1.562-2.421-3.639-2.421-5.846S4.86,7.983,6.422,6.421c1.561-1.562,3.636-2.422,5.844-2.422s4.284,0.86,5.845,2.422c1.562,1.562,2.422,3.638,2.422,5.845s-0.859,4.283-2.422,5.846c-1.562,1.562-3.636,2.42-5.845,2.42S7.981,19.672,6.422,18.111z" />
                            </g>
                        </svg></span>
                    <input type="text" class="form-control" placeholder="search your product">
                    <span class="input-group-text close-searchbar"><svg viewBox="0 0 329.26933 329"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="m194.800781 164.769531 128.210938-128.214843c8.34375-8.339844 8.34375-21.824219 0-30.164063-8.339844-8.339844-21.824219-8.339844-30.164063 0l-128.214844 128.214844-128.210937-128.214844c-8.34375-8.339844-21.824219-8.339844-30.164063 0-8.34375 8.339844-8.34375 21.824219 0 30.164063l128.210938 128.214843-128.210938 128.214844c-8.34375 8.339844-8.34375 21.824219 0 30.164063 4.15625 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921875-2.089844 15.082031-6.25l128.210937-128.214844 128.214844 128.214844c4.160156 4.160156 9.621094 6.25 15.082032 6.25 5.460937 0 10.921874-2.089844 15.082031-6.25 8.34375-8.339844 8.34375-21.824219 0-30.164063zm0 0" />
                        </svg></span>
                </div>
            </div>
        </div>
    </header>
    <!--header end-->

    <?= $content; ?>


    <!-- footer start -->
    <footer>
        <div class="subfooter dark-footer py-5">
            <div class="container">
                <div class="row">
                    <div class="col-xl-6 col-md-8 col-sm-12">
                        <div class="footer-left">
                            <p>2025 hệ thống trà sữa LEO TEA</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>
    <!-- footer end -->



    <!--Quickview product  modal popup start-->
    <div class="modal fade bd-example-modal-md theme-modal" id="exampleModal" tabindex="-1" role="dialog"
        aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="news-latter">
                        <div class="modal-bg">
                            <div class="newslatter-main">
                                <div class="offer-content">
                                    <div>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                        <div class="collection-product-wrapper">
                                            <div class="product-wrapper-grid product list-view" style="opacity: 1;"
                                                id="quickViewOrderProduct">

                                            </div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!--Newsletter Modal popup end-->





    <!-- Add to cart bar -->
    <div id="cart_side" class="add_to_cart right ">

    </div>
    <!-- Add to cart bar end-->


    <!-- My account bar start-->
    <div id="myAccount" class="add_to_cart right account-bar">
        <a href="javascript:void(0)" class="overlay" onclick="closeAccount()"></a>
        <div class="cart-inner">
            <div class="cart_top">
                <h3>Đăng nhập</h3>
                <div class="close-cart">
                    <a href="javascript:void(0)" onclick="closeAccount()">
                        <i class="fa fa-times" aria-hidden="true"></i>
                    </a>
                </div>
            </div>
            <form class="theme-form">
                <div class="form-group">
                    <label for="email">Tài khoản:</label>
                    <input type="text" class="form-control" id="loginUser" placeholder="Tài khoản" required="">
                </div>
                <div class="form-group">
                    <label for="review">Mật khẩu</label>
                    <input type="password" class="form-control" id="loginPass" placeholder="Mật khẩu" required="">
                </div>
                <div class="form-group">
                    <a href="javascript:void(0)" class="btn btn-solid btn-md btn-block" onclick="login(false)">Login</a>
                </div>
            </form>
        </div>
    </div>


    <script src="<?= PATH_URL; ?>assets/js/frontend/index.js"></script>
    <!-- latest jquery-->
    <script src="<?= PATH_URL; ?>assets/js/frontend/jquery-3.3.1.min.js"></script>

    <!-- slick js-->
    <script src="<?= PATH_URL; ?>assets/js/frontend/slick.js"></script>

    <!-- gallary js -->
    <script src='<?= PATH_URL; ?>assets/js/frontend/gallery.js'></script>



    <!-- tool tip js -->
    <script src="<?= PATH_URL; ?>assets/js/frontend/tippy-popper.min.js"></script>
    <script src="<?= PATH_URL; ?>assets/js/frontend/tippy-bundle.iife.min.js"></script>

    <!-- popper js-->
    <script src="<?= PATH_URL; ?>assets/js/frontend/popper.min.js"></script>

    <!-- Timer js-->
    <script src="<?= PATH_URL; ?>assets/js/frontend/menu.js"></script>

    <!-- Bootstrap js-->
    <script src="<?= PATH_URL; ?>assets/js/frontend/bootstrap.js"></script>

    <!-- father icon -->
    <script src="<?= PATH_URL; ?>assets/js/frontend/feather.min.js"></script>
    <script src="<?= PATH_URL; ?>assets/js/frontend/feather-icon.js"></script>

    <!-- Bootstrap js-->
    <script src="<?= PATH_URL; ?>assets/js/frontend/bootstrap-notify.min.js"></script>

    <!-- Theme js-->
    <!-- <script src="<?= PATH_URL; ?>assets/js/frontend/slider-animat-nine.js"></script> -->

    <script src="<?= PATH_URL; ?>assets/js/frontend/modal.js"></script>
    <script src="<?= PATH_URL; ?>assets/js/frontend/jquery.number.js"></script>
    <script src="<?= PATH_URL; ?>assets/js/admin/jquery.form.js"></script>
    <script src="<?= PATH_URL; ?>assets/js/frontend/script.js"></script>
    <script type="text/javascript">
    var root = '<?=PATH_URL?>';
    var csrf_token;
    </script>
</body>

</html>