<script>
function submit() {
    let orderCode = $('#orderCode').val();
    let note = $('#note').val();
    if (orderCode === "" || note === "") {
        notify('Vui lòng nhập thông tin.', 'danger', true);
        return false;
    }
    const btn = document.getElementById('btnSubmit');
    var url = root + 'updateCancelOrder';
    $.post(url, {
        orderCode: orderCode,
        note: note,
        csrf_token: $('#csrf_token').val()
    }, function(res) {
        console.log(res);
        $('#csrf_token').val(res.key);
        if (res.status) {
            notify('Yêu cầu xử lý hóa đơn đã gửi thành công.', 'primary', true);
            // Vô hiệu hóa nút
            btn.disabled = true;
        } else {
            notify(res.mes, 'danger', true);
            btn.disabled = false;
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
                    <h2>Thông tin hóa đơn</h2>
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
                        <h4>Nhập mã hóa đơn hủy</h4>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 offset-lg-3">
                <div class="row order-success-sec">

                    <div class="input-block mb-2">
                        <h4>Lý do hủy:</h4>
                        <textarea rows="2" id="note" class="form-control"></textarea>
                    </div>
                    <div class="input-group mb-5">
                        <input type="text" class="form-control" placeholder="Mã order" id="orderCode">
                        <button class="btn btn-normal" onclick="submit()" id="btnSubmit"> Xác nhận</button>
                    </div>
                </div>


            </div>
        </div>
    </div>
</section>
<!--order tracking end-->