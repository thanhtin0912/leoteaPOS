

<script>
    $(document).ready(function() {
        $('#user-input').on('input', function() {
            let value = $(this).val();
            // 1. Sửa "NCD" thành "NFD" để tách dấu ra khỏi chữ cái
            value = value.normalize("NFD").replace(/[\u0300-\u036f]/g, "");
            // Xử lý riêng cho chữ đ và Đ
            value = value.replace(/đ/g, "d").replace(/Đ/g, "D");
            // 2. Xóa các ký tự đặc biệt và khoảng trắng còn lại
            let cleanValue = value.replace(/[^a-zA-Z0-9]/g, '');
            $(this).val(cleanValue);
        });
    });
    function checkIn() {
        let user = $('#user-input').val();
        if (user == "") {
            notify('Vui lòng nhập tên nhân viên.', 'danger', true); 
            $('#loginUser').focus();
            return false;
        }
        var url = root + 'checkIn';
        $.post(url, {
            user: $('#user-input').val(),
            csrf_token: $('#csrf_token').val()
        }, function(res) {
            console.log(res);
            $('#csrf_token').val(res.key);
            if(res.status) {
                notify('Bạn đã vào ca thành công.', 'primary', true);
                const btn = document.getElementById('btnUser');
                // Vô hiệu hóa nút
                btn.disabled = true;
            } else {
                notify('Hệ thống không thể ghi nhận thông tin vào ca của bạn.', 'danger', true); 
            }
        });        
    }

</script>
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
  <div class="container order-tracking-box" >
    <div class="row">
        <div class="col-12">
            <div class="order-payment">
                <div class="title6">
                    <h4>Nhập thông tin xác nhận vào ca</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-6 offset-lg-3">
            <?php if(!$checkShift) {?>
            <div class="input-group  mb-5">
                <input type="text" class="form-control" placeholder="Nhập tên nhân viên" id="user-input">                                
                <button class="btn btn-normal" onclick="checkIn()" id="btnUser"> Xác nhận</button>                               
            </div>
            <?php } else { ?>
                <h4>Tài khoản chưa KẾT CA không thể VÀO CA mới.</h4>
            <?php } ?>
        </div>
    </div>
  </div>
</section>
<!--order tracking end-->