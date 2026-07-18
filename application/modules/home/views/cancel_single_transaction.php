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
            if (val.name === "Latte") return;
            const block = document.createElement('div');
            let unit = val.name; // Lấy tên mệnh giá từ val.name
            if (unit === "L") {
                unit = "L-Latte";
            }
            block.className = 'coupan-block';
            block.innerHTML = `
                <h5>${unit}</h5>
                <input class="form-control size-input" type="number" min="0" value="0" data-unit="${unit}" style="width: 70px; display: inline-block;">
            `;
            containerSizes.appendChild(block);
        });

        // Hiển thị dấu phẩy ngăn cách hàng nghìn khi nhập số tiền
        $('#amount-input').on('input', function() {
            const raw = ($(this).val() || '').replace(/[^\d]/g, '');
            if (!raw) {
                $(this).val('');
                return;
            }
            $(this).val(raw.replace(/\B(?=(\d{3})+(?!\d))/g, ','));
        });

    });
    function saveCancelSingleOrder() {
        let dataSizes = {};
        // Duyệt qua tất cả các ô nhập liệu
        document.querySelectorAll('.size-input').forEach(input => {
            const unit = input.getAttribute('data-unit');
            const quantity = parseInt(input.value) || 0;
            if (quantity !== 0 && input.value !== '') dataSizes[unit] = quantity;
        });
        const amountRaw = ($('#amount-input').val() || '').replace(/[^\d]/g, '');
        const amount = amountRaw ? parseInt(amountRaw, 10) : 0;
        var url = root + 'saveCancelSingleOrder';
        $.post(url, {
            dataSizes: dataSizes,
            user: $('#user-input').val(),
            amount: amount,
            note: $('#note-input').val(),
            csrf_token: $('#csrf_token').val()
        }, function(res) {
            $('#csrf_token').val(res.key);
            if(res.status) {
                notify('Lệnh hủy đơn thành công. Vui lòng chờ hệ thống xác nhận.', 'primary', true);
                const btn = document.getElementById('btnSave');
                btn.disabled = true;
                window.location.reload();
            } else {
                notify(res.mes || 'Hệ thống không thể ghi nhận thông tin hủy đơn của bạn.', 'danger', true);
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
                    <h4>Nhập thông tin cần hủy</h4>
                </div>
            </div>
        </div>
        <div class="col-lg-6 offset-lg-3">
            <div class="order-tracking-sidebar order-tracking-box p-0">
                <?php if($checkShift && $this->session->userdata('userLogin')) {?>
                <div class="input-group">
                    <div id="sizes-container" class="d-flex flex-wrap gap-3">
                        <!-- Các block mệnh giá tiền sẽ được tạo ra ở đây -->
                        
                    </div>                              
                </div>
                <div class="input-group mb-3">
                    <textarea class="form-control" rows="3" placeholder="Nội dung hủy" id="note-input"></textarea>
                </div>
                <div class="input-group  mb-5">
                    <input type="text" class="form-control" placeholder="Số tiền" id="amount-input">                                
                    <button class="btn btn-normal" onclick="saveCancelSingleOrder()" id="btnSave"> Xác nhận</button>

                </div>
            <?php } else { ?>
                <h4>Hiện tại bạn không có ca làm việc nào hoặc chưa login.</h4>
            <?php } ?>
            </div>
        </div>
    </div>
   <?php if($transactionNotVerify) { ?>
    <div class="row">
        <div class="col-12">
                            <table class="table cart-table table-responsive-xs">
                    <thead>
                    <tr class="table-head">
                        <th scope="col">Số tiền</th>
                        <th scope="col">Số ly</th>
                        <th scope="col">Ghi chú</th>
                        <th scope="col">Trạng thái</th>
                        <th scope="col">Ngày tạo</th>
                    </tr>
                    </thead>
                    <?php foreach ($transactionNotVerify as $transaction) { ?>
                    <tbody>
                    <tr>
                        <td><?= number_format($transaction->amount_price) ?></td>
                        <td><?php $amount_sizes = unserialize($transaction->amount_sizes); if(is_array($amount_sizes)) { foreach($amount_sizes as $size) { echo $size->name . ': ' . $size->in . '<br>'; } } ?></td>
                        <td><?= $transaction->note ?></td>
                        <td><span class="badge bg-warning">Chờ xác nhận</span></td>
                        <td><?= date('H:i:s', strtotime($transaction->created)) ?></td>
                    </tr>
                    </tbody>
                    <?php } ?>
                </table>
        </div>
    </div>
    <?php } ?>
</section>
