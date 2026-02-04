<!-- thank-you section start -->
<script>
    $(document).on('change', 'input[name="orderType"]', function() {
        let rawValue = $('#subTotalPrice').text(); 
        let subTotal = Number(rawValue.replace(/\D/g, ''));
        if ($(this).val() === 'Delivery') {
            let store = <?php echo json_encode($store); ?>;
            if(subTotal < store['condition']) {
                let grandTotal = formatCurrency(subTotal + Number(store['shippingfee']));
                $('#grandTotalPrice').html(grandTotal);  
                $('#shippingPrice').html(formatCurrency(store['shippingfee'])); 
            }
        } else {
            $('#shippingPrice').html(0);
            $('#grandTotalPrice').html(rawValue);   
        }
    });
    function formatCurrency(value) {
        let amount = Number(value);
        return new Intl.NumberFormat('en-US').format(amount);
    }
</script>
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>XÁC NHẬN HÓA ĐƠN</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->


<!-- order-detail section start -->
<section class="section-big-py-space mt-5 b-g-light mb-5">
    <div class="custom-container">
        <div class="row">
            <div class="col-lg-6">
                <div class="product-order">
                    <h3>Chi tiết đơn hàng</h3>
                    <?php $total = 0; ?>
                    <?php foreach ($cart as $key => $v): ?>
                    <div class="row product-order-detail py-2">
                        <div class="col-3"><img src="<?=GLOBAL_URL.$v->image ?>" alt="" class="img-fluid "></div>
                        <div class="col-4 order_detail">
                            <div>
                                <h4><?= $v->name; ?> <?php if ($v->size != '') { echo "(".$v->size.")";}?></h4>
                                <?php if($v->priceTopping > 0) { ?>
                                <h5><?= $v->topping; ?></h5>
                                <?php } ?>
                                <?php if($v->note!='' || $v->note!= NULL) { ?>
                                <h5 style="font-style: italic">*Note: <?= $v->note; ?></h5>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-1 order_detail">
                            <div>
                                <h4>SL</h4>
                                <h5><?= $v->amount; ?></h5></div>
                        </div>
                        <div class="col-3 order_detail justify-content-end">
                            <div>
                                <h4>Đơn giá</h4>
                                <h5><?php echo number_format($v->totalPrice); ?></h5></div>
                        </div>
                        <div class="col-1 order_detail justify-content-end">
                            <div>
                                <h5>
                                    <a href="javascript:void(0)" onclick="removecart(<?=$v->id;?>,<?=$key;?>)"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash-2"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg></a>
                                </h5>
                            </div>
                        </div>
                    </div>
                    <?php $total = $total + ($v->totalPrice); ?>
                    <?php endforeach ?>
                    <div class="total-sec fw-bold">
                        <ul>
                            <li>Tổng tiền <span id="subTotalPrice"><?= number_format($total); ?></span></li>
                            <li>Phí giao hàng <span id="shippingPrice">0</span></li>
                        </ul>
                    </div>
                    <div class="final-total pt-2">
                        <h3>Thành tiền <span id="grandTotalPrice"><?= number_format($total); ?></span></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row order-success-sec">

                    <div class="col-sm-12">
                        <h4>Loại đơn hàng</h4>
                        <form class="size-new py-3">
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="Eatin" checked name="orderType" id="orderType1" class="size-radio-input" data-size="0">
                                <label for="orderType1" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Eatin</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="Takeaway" name="orderType" id="orderType2" class="size-radio-input" data-size="0">
                                <label for="orderType2" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Takeaway</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="Delivery" name="orderType" id="orderType3" class="size-radio-input" data-size="0">
                                <label for="orderType3" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Delivery</p>
                                    </div>
                                </label>
                            </div>

                        </form>
                    </div>
                    <div class="col-sm-12">
                        <h4>ghi chú</h4>
                        <textarea rows="3" id="note" class="form-control"></textarea>
                    </div>
                    <div class="col-sm-12 payment-mode">

                        <div class="delivery-sec">
                            <h2><?= date("Y-m-d H:m",time());?></h2></div>
                    </div>
                    
                    <div class="col-12 pt-5 text-center">
                        <button class="btn btn-normal btn-sm" onclick="checkout();" id="btnCheckout">Xác nhận đơn hàng</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->