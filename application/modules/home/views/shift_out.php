
<script src="<?= PATH_URL; ?>assets/js/frontend/qrious.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    updateClock();
});

function updateClock() {
    const now = new Date();
    // Lấy giờ, phút, giây và định dạng luôn có 2 chữ số (ví dụ: 09 thay vì 9)
    const hours = String(now.getHours()).padStart(2, '0');
    const minutes = String(now.getMinutes()).padStart(2, '0');
    const seconds = String(now.getSeconds()).padStart(2, '0');
    const currentTime = hours + ":" + minutes + ":" + seconds;
    // Gán giá trị vào input
    $('#clock-input').html(currentTime);
}

function checkOut() {
        let id = $('#idShift').val();
        var url = root + 'checkOutShift';
        $.post(url, {
            id: id,
            csrf_token: $('#csrf_token').val()
        }, function(res) {
            $('#csrf_token').val(res.key);
            if(res.status) {
                notify('Bạn đã kết ca .Vui lòng quét mã QR để báo cáo.', 'primary', true);
                const btn = document.getElementById('btnSubmit');
                // Vô hiệu hóa nút
                btn.disabled = true;
                var qr = new QRious({
                    element: document.getElementById('qr-code'),
                    value: root+'bao-cao-ca-lam-viec?id='+res.id,
                    size: 250
                });
                $('#myLink').text(root+'bao-cao-ca-lam-viec?id='+res.id).attr('href', root+'bao-cao-ca-lam-viec?id='+res.id);
            } else {
                notify('Hệ thống không thể ghi nhận thông tin vào ca của bạn.', 'danger', true); 
            }
        });        
    }
</script>
<style>
    /* Làm cho khung bao phủ toàn bộ màn hình */
.qr-wrapper {
    display: flex;
    justify-content: center; /* Căn giữa theo chiều ngang */
    align-items: center;     /* Căn giữa theo chiều dọc */
    min-height: 50vh;       /* Chiều cao bằng 100% màn hình */
    background-color: #f8f9fa; /* Màu nền nhẹ (tùy chọn) */
}

/* Tùy chỉnh thêm cho canvas nếu muốn */
#qr-code {
    box-shadow: 0 4px 10px rgba(0,0,0,0.1); /* Đổ bóng cho đẹp */
    border: 10px solid white;               /* Tạo viền trắng xung quanh */
    border-radius: 8px;
}
</style>
<!-- thank-you section start -->
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>Quản lý ca làm việc</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->
<!--order tracking start-->
<section class="order-tracking section-big-my-space my-5 mb-5">
    <div class="container order-tracking-box">
        <div class="row">
            <div class="col-12">
                <div class="order-payment">
                    <div class="title6">
                        <h4>Thông tin xác nhận kết ca</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-3">
                
                <div class="order-tracking-sidebar order-tracking-box">
                    <?php if($checkShift) {?>
                    <input type="hidden" id="idShift" value="<?= $checkShift[0]->id; ?>"/>
                    <ul class="cart_total">
                        <li>
                            <div class="total">
                                Tên nhân viên:<span><?= $checkShift[0]->name; ?></span>
                            </div>
                            <div class="total">
                                Giờ vào ca:<span><?= $checkShift[0]->from; ?></span>
                            </div>
                            <div class="total">
                                Giờ kết ca:<span id="clock-input"></span>
                            </div>
                        </li>
                        <li class="py-3">
                            <div class="buttons">
                                <button class="btn btn-solid btn-sm btn-block" onclick="checkOut();" id="btnSubmit">Xác nhận</button>
                            </div>
                        </li>
                        <div class="qr-wrapper">
                            <canvas id="qr-code"></canvas>
                        </div>
                        <a id="myLink" href="#">Click vào đây</a>
                    </ul>
                    <?php } else { ?>
                        <h4>Bạn chưa có thông tin VÀO CA.</h4>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<!--order tracking end-->