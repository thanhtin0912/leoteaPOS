<!DOCTYPE html>
<html lang="en">

<head>
    <title>Xác nhận hủy phiếu thanh toán</title>
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
    <link rel="stylesheet" href="<?= PATH_URL; ?>assets/css/frontend/jquery-confirm.min.css">
    <script src="<?= PATH_URL; ?>assets/js/frontend/jquery-1.9.1.min.js"></script>
    
</head>
<style>
    .coupan-block input {
        width: 100px !important; 
    }
</style>
<script>
window.addEventListener('DOMContentLoaded', (event) => {
    document.getElementById('save-data').addEventListener('click', function() {   
        let id = $('#idOrder').val();                  
        var url = root + 'verifyCancelOrder';
        $.ajax({
            url: url,
            type: 'POST',
            data: { 
                id: id,
                csrf_token: $('#csrf_token').val()
            },
            success: function(res) {
                $('#csrf_token').val(res.key);
                if(res.status) {
                    notify('Hóa đơn đã được HỦY thành công.', 'primary', true);
                    const btn = document.getElementById('save-data');
                    // Vô hiệu hóa nút
                    btn.disabled = true;
                } else {
                    notify('Hệ thống không thể ghi nhận thông tin HỦY hóa đơn.', 'danger', true); 
                }
            }
        });

    });
})
</script>
<body class="bg-light">
    <input type="hidden" value="<?= $this->security->get_csrf_hash() ?>" id="csrf_token" />
    <!-- loader start -->
    <div class="loader-wrapper">
        <div>
            <img src="<?= PATH_URL; ?>assets/images/giphy1.gif" alt="loader">
        </div>
    </div>
    <!-- loader end -->
    <section class="order-tracking">
        <div class="container order-tracking-box">
            <div class="row">
                <div class="col-12">
                    <div class="order-payment">
                        <div class="title6">
                            <h4>Xác nhận hủy phiếu thanh toán</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-3">
                    <div class="order-tracking-sidebar order-tracking-box">
                        <?php if($res) {?>
                        <input type="hidden" id="idOrder" value="<?= $res[0]->id; ?>" />
                        <ul class="cart_total">
                            <li>
                                <div class="total">
                                    Mã phiếu:<span><?= $res[0]->orderId; ?></span>
                                </div>
                                <div class="total">
                                    Ngày tạo:<span><?= $res[0]->created; ?></span>
                                </div>
                                <div class="total">
                                    Tổng:<span><?= number_format($res[0]->grandtotal); ?></span>
                                </div>
                                <div class="total">
                                    Lý do:<span><?= $res[0]->note; ?></span>
                                </div>
                            </li>
                            <li class="py-3">
                                <div class="buttons">
                                    <button class="btn btn-solid btn-sm btn-block" id="save-data">Xác nhận</button>
                                </div>
                            </li>
                            
                        </ul>
                        <?php } else { ?>
                        <h4 class="text-center">Hóa đơn không tìm thấy hoặc đã thực hiện xác nhận.</h4>
                        <?php } ?>
                    </div>

                </div>
            </div>
        </div>
    </section>
    <!--order tracking end-->


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





    <script src="<?= PATH_URL; ?>assets/js/frontend/jquery-3.3.1.min.js"></script>

    <script src="<?= PATH_URL; ?>assets/js/frontend/jquery-confirm.min.js"></script>

    <script src="<?= PATH_URL; ?>assets/js/frontend/index.js"></script>
    <!-- latest jquery-->


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