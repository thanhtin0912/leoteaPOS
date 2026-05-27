<!DOCTYPE html>
<html lang="en">

<head>
    <title>Quản lý báo cáo ca</title>
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
    .coupan-block h5 {
        color: #777 !important;
    }
</style>
<script>

window.addEventListener('DOMContentLoaded', (event) => {
    // 1. Danh sách các mệnh giá tiền
    const denominations = [500000, 200000, 100000, 50000, 20000, 10000, 5000, 2000, 1000];
    const container = document.getElementById('coupan-container');
    const grandTotalDisplay = document.getElementById('grand-total');
    // 2. Hàm tạo giao diện
    denominations.forEach(val => {
        const block = document.createElement('div');
        block.className = 'coupan-block';
        block.innerHTML = `
            <h5>${val.toLocaleString('vi-VN')}</h5>
            <input class="form-control qty-input" type="number" value="0" data-unit="${val}" style="width: 100px; display: inline-block;">
            <h5><span class="row-total">0</span></h5>
        `;
        container.appendChild(block);
    });

    // 3. Hàm tính toán khi nhập liệu
    container.addEventListener('input', function(e) {
        if (e.target.classList.contains('qty-input')) {
            const input = e.target;
            const unitValue = parseInt(input.getAttribute('data-unit'));
            const quantity = parseInt(input.value) || 0;
            // Tính tiền của dòng đó
            const rowTotal = unitValue * quantity;
            input.parentElement.querySelector('.row-total').innerText = rowTotal.toLocaleString('vi-VN');
            // Tính tổng tất cả các dòng
            calculateGrandTotal();
        }
    });
    function calculateGrandTotal() {
        let total = 0;
        document.querySelectorAll('.qty-input').forEach(input => {
            total += (parseInt(input.value) || 0) * parseInt(input.getAttribute('data-unit'));
        });
        grandTotalDisplay.innerText = total.toLocaleString('vi-VN');
    }
    document.getElementById('save-data').addEventListener('click', function() {
        let dataResult = {}; // Đây là nơi chứa kết quả ['mệnh giá': số lượng]
        // Duyệt qua tất cả các ô nhập liệu
        document.querySelectorAll('.qty-input').forEach(input => {
            const unit = input.getAttribute('data-unit'); // Lấy mệnh giá (Key)
            const quantity = parseInt(input.value) || 0;  // Lấy số lượng (Value)
            
            // Chỉ lưu những mệnh giá nào có số lượng lớn hơn 0 (tùy chọn)
            if (quantity > 0) {
                dataResult[unit] = quantity;
            }
        });
        let spent = $('#spent').val();
        let tip = $('#tip').val();
        // nếu có giá trị và nhỏ hơn 1000 thì báo lỗi
        if (tip!=0 && parseInt(tip) < 1000) {
            notify('Số tiền tip phải lớn hơn hoặc bằng 1.000 VNĐ.', 'warning', true);
            return;
        }
        if (spent!=0 && parseInt(spent) < 1000) {
            notify('Số tiền chi tiêu phải lớn hơn hoặc bằng 1.000 VNĐ.', 'warning', true);
            return;
        }
        let cash = parseInt(grandTotalDisplay.innerText.replace(/\./g, ''));
        let cashSales = <?= $salesShift ?>;
        let id = <?= $res[0]->id ?>;
        let str = "Thiếu";
        let cashActual = cash - parseInt(tip) + parseInt(spent);
        if(cashActual === cashSales) {
            str = "Đủ";
        } else if ( cashActual > cashSales ) {
            str = "Thừa";
        }
        const btn = document.getElementById('save-data');
        btn.style.display = 'none';
        $.confirm({
            title: 'Báo cáo kết ca!',
            content: `Tổng số tiền mặt bạn đã đếm là: <b>${cash.toLocaleString('vi-VN')} VNĐ</b> <b>(${str})</b>.<br>Bạn có chắc chắn muốn nộp báo cáo không?`,
            type: 'success',
            typeAnimated: true,
            buttons: {
                confirm: {
                    text: 'Xác nhận nộp',
                    btnClass: 'btn-success',
                    action: function () {
                        let spentNote = $('#spentNote').val();
                        var url = root + 'updateCheckoutShift';
                        $.ajax({
                            url: url,
                            type: 'POST',
                            data: { 
                                id: id,
                                money_data: dataResult,
                                actual: cash,
                                spent: spent,
                                tip: tip,
                                spentNote: spentNote,
                                csrf_token: $('#csrf_token').val()
                            },
                            success: function(res) {
                                $('#csrf_token').val(res.key);
                                if(res.status) {
                                    notify('Bạn đã nộp doanh thu thành công.', 'primary', true);

                                } else {
                                    notify('Hệ thống không thể ghi nhận thông tin doanh thu của bạn.', 'danger', true); 
                                    btn.style.display = 'inline-block';
                                }
                            }
                        });
                    }
                }
            }
        }); 
    });
})
function calculateGrandTotal() {
    let total = 0;
    document.querySelectorAll('.qty-input').forEach(input => {
        total += (parseInt(input.value) || 0) * parseInt(input.getAttribute('data-unit'));
    });
    grandTotalDisplay.innerText = total.toLocaleString('vi-VN');
}
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
                            <h4>Thông tin báo cáo ca làm việc</h4>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 offset-lg-3">
                    <div class="order-tracking-sidebar order-tracking-box">
                        <?php if($res) {?>
                        <input type="hidden" id="idShift" value="<?= $res[0]->id; ?>" />
                        <ul class="cart_total">
                            <li>
                                <div class="total">
                                    Tên nhân viên:<span><?= $res[0]->name; ?></span>
                                </div>
                                <div class="total">
                                    Giờ vào ca:<span><?= $res[0]->from; ?></span>
                                </div>
                                <div class="total">
                                    Giờ kết ca:<span><?= $res[0]->to; ?></span>
                                </div>
                                <div class="total pb-0"></div>
                            </li>
                        </ul>
                        <div class="coupan-block">
                            <h5>Tiền Tip</h5>
                            <input class="form-control" value=0 style="width: 150px !important" id="tip">
                        </div>
                        <div class="coupan-block">
                            <h5>Chi phí khác</h5>
                            <input class="form-control" value=0 style="width: 150px !important" id="spent">
                        </div>
                        <div class="coupan-block">
                            <h5>Nội dung</h5>
                            <input class="form-control" style="width: 150px !important" id="spentNote">
                        </div>
                        <div id="coupan-container">
                            
                        </div>
                        <ul class="cart_total">
                            <li>
                                <div class="fw-bold ">
                                Tổng cộng <span id="grand-total">0</span>
                                </div>
                            </li>
                            <li class="py-3">
                                <div class="buttons">
                                    <button class="btn btn-solid btn-sm btn-block" id="save-data">Xác nhận</button>
                                </div>
                            </li>
                            
                        </ul>
                        <?php } else { ?>
                        <h4 class="text-center">Không có ca làm việc nào được ghi nhận.</h4>
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