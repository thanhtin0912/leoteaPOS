<!DOCTYPE html>
<html lang="en">

<head>
    <title>Bigdeal - Multi-purpopse E-commerce Html Template</title>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
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
                                    <a href="javascript:void(0)"><i class="fa fa-phone"></i>Hotline: <?=$info[0]->phone ? $info[0]->phone : '' ?></a>
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
                                
                                <?php if ($this->session->userdata('userLogin')) {?>
                                <li>
                                    <a href="javascript:;"><i class="fa fa-shopping-cart"></i>TK: <?= $this->session->userdata('userLogin')->phone;?></a>
                                </li>
                                <?php } ?>
                                <?php if ($this->session->userdata('staffName')) {?>
                                <li>
                                    <a href="javascript:void(0)"><i class="fa fa-sign-in"></i>TN: <?=($this->session->userdata('staffName')) ?></a>
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
                                            <a class="dark-menu-item" href="<?= PATH_URL; ?>trang-thai-don-hang">Xử lý</a>
                                        </li>
                                        <!--SHOP-END-->
                                        <!--Shift-->
                                        <?php if ($this->session->userdata('userLogin')) {?>
                                        <li>
                                            <a class="dark-menu-item" href="javascript:void(0)">Chấm công</a>
                                            <ul>
                                                <li><a href="<?= PATH_URL; ?>vao-ca">Vào ca</a></li>
                                                <li><a href="<?= PATH_URL; ?>ket-ca">Kết ca</a></li>
                                            </ul>
                                        </li>
                                        <?php } ?>
                                        <!--Shift-END-->
                                        <!--cancel order-->
                                        <li>
                                            <a class="dark-menu-item" href="<?= PATH_URL; ?>huy-hoa-don">Hóa đơn</a>
                                        </li>
                                        <!--cancel-order-END-->
                                        <!--cancel order-->
                                        <li>
                                            <a class="dark-menu-item" href="<?= PATH_URL; ?>huy-mot-phan-hoa-don">Hủy chi tiết HD</a>
                                        </li>
                                        <!--cancel-order-END-->
                                        <li>
                                            <a href="javascript:;" onclick="syncProduct()">Đồng bộ</a>
                                        </li>
                                        <?php if (!$this->session->userdata('userLogin')) {?>
                                        <li onclick="openAccount()">
                                            <a href="javascript:void(0)">Đăng nhập</a>
                                        </li>
                                        <?php } else { ?>
                                        <li onclick="logOut()">
                                            <a href="<?= PATH_URL; ?>logout">Đăng xuất</a>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </nav>
                            </div>
                            <div class="icon-block">
                                <ul class="theme-color icon-radius">
                                    <li class="mobile-search item-count" onclick="openWishlist()">
                                        <svg viewBox="0 -28 512.001 512" xmlns="http://www.w3.org/2000/svg">
                                            <path
                                                d="m256 455.515625c-7.289062 0-14.316406-2.640625-19.792969-7.4375-20.683593-18.085937-40.625-35.082031-58.21875-50.074219l-.089843-.078125c-51.582032-43.957031-96.125-81.917969-127.117188-119.3125-34.644531-41.804687-50.78125-81.441406-50.78125-124.742187 0-42.070313 14.425781-80.882813 40.617188-109.292969 26.503906-28.746094 62.871093-44.578125 102.414062-44.578125 29.554688 0 56.621094 9.34375 80.445312 27.769531 12.023438 9.300781 22.921876 20.683594 32.523438 33.960938 9.605469-13.277344 20.5-24.660157 32.527344-33.960938 23.824218-18.425781 50.890625-27.769531 80.445312-27.769531 39.539063 0 75.910156 15.832031 102.414063 44.578125 26.191406 28.410156 40.613281 67.222656 40.613281 109.292969 0 43.300781-16.132812 82.9375-50.777344 124.738281-30.992187 37.398437-75.53125 75.355469-127.105468 119.308594-17.625 15.015625-37.597657 32.039062-58.328126 50.167969-5.472656 4.789062-12.503906 7.429687-19.789062 7.429687zm-112.96875-425.523437c-31.066406 0-59.605469 12.398437-80.367188 34.914062-21.070312 22.855469-32.675781 54.449219-32.675781 88.964844 0 36.417968 13.535157 68.988281 43.882813 105.605468 29.332031 35.394532 72.960937 72.574219 123.476562 115.625l.09375.078126c17.660156 15.050781 37.679688 32.113281 58.515625 50.332031 20.960938-18.253907 41.011719-35.34375 58.707031-50.417969 50.511719-43.050781 94.136719-80.222656 123.46875-115.617188 30.34375-36.617187 43.878907-69.1875 43.878907-105.605468 0-34.515625-11.605469-66.109375-32.675781-88.964844-20.757813-22.515625-49.300782-34.914062-80.363282-34.914062-22.757812 0-43.652344 7.234374-62.101562 21.5-16.441406 12.71875-27.894532 28.796874-34.609375 40.046874-3.453125 5.785157-9.53125 9.238282-16.261719 9.238282s-12.808594-3.453125-16.261719-9.238282c-6.710937-11.25-18.164062-27.328124-34.609375-40.046874-18.449218-14.265626-39.34375-21.5-62.097656-21.5zm0 0" />
                                        </svg>
                                        <div class="item-count-contain inverce" id="count-hold">
                                        <?= $countHold ?? 0 ?>
                                        </div>
                                    </li>
                                    <li class="mobile-wishlist d-block d-sm-none">
                                        <div class="list-delivery-method d-flex">
                                            <div class="deliery-method-card d-flex active">
                                                <div class="deliery-method-card__image">
                                                    <a href="<?= PATH_URL; ?>"><img width="40px"
                                                            src="<?= PATH_URL; ?>assets/images/giphy1.gif" alt=""></a>
                                                </div>
                                            </div>

                                        </div>
                                        
                                    </li>
                                    <li class="cart-block mobile-cart item-count" onclick="openCart()">
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

            <div id="wishlist_side" class="add_to_cart right ">

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
                            <p>2025@copy right by Leotea</p>
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
    function syncProduct(){
        var userLogin = <?php echo json_encode($this->session->userdata('userLogin')); ?>;
        if (userLogin) {
            notify('Đăng xuất khỏi hệ thống trước khi đồng bộ', 'danger', true);
            return false;
        }
        setTimeout(() => {
            $('.loader-wrapper').addClass('active');
        }, 2);

        $.post(root+'home/syncProduct',{
            csrf_token:     $('#csrf_token').val()
        },function(res){
            $('#csrf_token').val(res.key);
            if(res.status) {
                notify('Đồng bộ data với server thành công', 'success', true);
            } else {
                notify('Không thể dồng bộ data, vui lòng liên hệ quản lý.', 'danger', true);
            }
            $('.loader-wrapper').removeClass('active');
        });

    }
    </script>
</body>

</html>