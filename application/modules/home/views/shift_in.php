<style>
    #sizes-container {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
    }

    #sizes-container .coupan-block {
        flex: 0 0 calc(50% - 6px);
        max-width: calc(50% - 10px);
        border: 1px solid #e5e5e5;
        border-radius: 6px;
        padding: 10px;
    }

    #sizes-container .coupan-block h5 {
        margin-bottom: 8px;
    }
</style>

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

            // 1. Danh sách các cups sizes từ PHP sang JavaScript
        const sizes = <?php echo json_encode($sizes); ?>;
       
        const containerSizes = document.getElementById('sizes-container');
        // 2. Hàm tạo giao diện
        sizes.forEach(val => {
            const block = document.createElement('div');
            block.className = 'coupan-block';
            block.innerHTML = `
                <h5>${val.name}</h5>
                <input class="form-control size-input" type="number" min="0" value="0" data-unit="${val.name}" style="width: 70px; display: inline-block;">
            `;
            containerSizes.appendChild(block);
        });
    });
    function checkIn() {
        let user = $('#user-input').val();
        if (user == "") {
            notify('Vui lòng nhập tên nhân viên.', 'danger', true); 
            $('#user-input').focus();
            return false;
        }
        let dataSizes = {}; // Đây là nơi chứa kết quả ['mệnh giá': số lượng]
        // Duyệt qua tất cả các ô nhập liệu
        document.querySelectorAll('.size-input').forEach(input => {
            const unit = input.getAttribute('data-unit');
            const quantity = parseInt(input.value) || 0;
            
            // Chỉ lưu những mệnh giá nào có số lượng lớn hơn 0 (tùy chọn)
            if (quantity > 0) {
                dataSizes[unit] = quantity;
            }
        });
        var url = root + 'checkIn';
        $.post(url, {
            dataSizes: dataSizes,
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
            <div class="order-tracking-sidebar order-tracking-box p-0">
                <?php if(!$checkShift) {?>
                <div class="input-group">
                    <div id="sizes-container" class="d-flex flex-wrap gap-3 mb-3">
                        <!-- Các block mệnh giá tiền sẽ được tạo ra ở đây -->
                        
                    </div>                              
                </div>
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
  </div>
</section>
<!--order tracking end-->