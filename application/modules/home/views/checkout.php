<!-- thank-you section start -->
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
                        <div class="col-3"><img src="<?=PATH_URL.DIR_UPLOAD_PRODUCT.$v->image ?>" alt="" class="img-fluid "></div>
                        <div class="col-4 order_detail">
                            <div>
                                <h4><?= $v->name; ?> <?php if ($v->size != '') { echo "(".$v->size.")";}?></h4>
                                <?php if($v->priceTopping > 0) { ?>
                                <h5><?= $v->topping; ?></h5>
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
                    <hr>
                    <?php $total = $total + ($v->totalPrice); ?>
                    <?php endforeach ?>
                    <div class="final-total pt-2">
                        <h3>Tổng tiền <span><?= number_format($total); ?></span></h3>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="row order-success-sec">
                    <div class="col-sm-12">
                        <h4>Loại đơn hàng</h4>
                        <form class="size-new py-3">
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="1" checked name="orderType" id="orderType1" class="size-radio-input" data-size="0">
                                <label for="orderType1" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Tại quán</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="2" name="orderType" id="orderType2" class="size-radio-input" data-size="0">
                                <label for="orderType2" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Mang về</p>
                                    </div>
                                </label>
                            </div>
                            <div class="card-product-option-item custom-radio mb-0">
                                <input type="radio" value="3" name="orderType" id="orderType3" class="size-radio-input" data-size="0">
                                <label for="orderType3" class="size-radio-label p-1">
                                    <div class="size-radio-content">
                                        <p class="size-name">Giao hàng</p>
                                    </div>
                                </label>
                            </div>
                        </form>
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