<?php if($count!= 0){?>
<a href="javascript:void(0)" class="overlay" onclick="closeCart()"></a>
<div class="cart-inner">
    <div class="cart_top">
        <h3>Đơn hàng</h3>
        <input type="hidden" value="<?=$count?>" id="count_cart" />
        <div class="close-cart">
            <a href="javascript:void(0)" onclick="closeCart()">
                <i class="fa fa-times" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="cart_media">
        <ul class="cart_product">
            <?php $total = 0; ?>
            <?php foreach ($cart as $key => $v): ?>
            <li>
                <div class="media">
                    <a href="javascript:void(0)">
                        <img alt="megastore1" class="me-3" src="<?=PATH_URL.DIR_UPLOAD_PRODUCT.$v->image ?>">
                    </a>
                    <div class="media-body">

                        <h4 class="mb-0"><?= $v->name; ?> <?php if ($v->size != '') { echo "(".$v->size.")";}?></h4>
                        <?php if($v->priceTopping > 0) { ?>
                        <h5 class="py-2"><?= $v->topping; ?></h5>
                        <?php } ?>
                        <h6><?php echo number_format($v->totalPrice); ?></h6>
                        <div class="addit-box d-flex justify-content-between">
                            <div class="qty-box">
                                <div class="input-group">
                                    <button class="qty-minus"></button>
                                    <input class="qty-adj form-control" type="number" value="<?=$v->amount?>" id="qtyItem<?=$key ?>"/>
                                    <button class="qty-plus"></button>
                                </div>
                            </div>
                            <div class="pro-add">
                                <a href="javascript:void(0)" data-bs-toggle="modal" onclick="updateItemCart(<?=$v->id;?>,<?=$key;?>)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-edit">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                </a>
                                <a href="javascript:void(0)" onclick="removecart(<?=$v->id;?>,<?=$key;?>)">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round" class="feather feather-trash-2">
                                        <polyline points="3 6 5 6 21 6"></polyline>
                                        <path
                                            d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                        </path>
                                        <line x1="10" y1="11" x2="10" y2="17"></line>
                                        <line x1="14" y1="11" x2="14" y2="17"></line>
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <?php $total = $total + ($v->totalPrice); ?>
            <?php endforeach ?>
        </ul>
        <ul class="cart_total">
            <li>
                <div class="total">
                    Tổng cộng<span><?= number_format($total); ?></span>
                </div>
            </li>
            <li>
                <div class="buttons">
                    <a href="<?=PATH_URL?>xac-nhan-don-hang" class="btn btn-solid btn-sm w-50 mx-1">Tính Tiền</a>
                    <a href="javascript:void(0)" onclick="removeAllCart()" class="btn btn-sm w-50 mx-1 btn-dark">Xóa
                        hết</a>
                </div>
            </li>
        </ul>
    </div>
</div>
<?php } else { ?>
<a href="javascript:void(0)" class="overlay" onclick="closeCart()"></a>
<div class="cart-inner">
    <div class="cart_top">
        <h3>Đơn hàng</h3>
        <input type="hidden" value="0" id="count_cart" />
        <div class="close-cart">
            <a href="javascript:void(0)" onclick="closeCart()">
                <i class="fa fa-times" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="cart_media border-0">
        <h4 class="text-center">Đơn hàng trống vui lòng chọn sản phẩm</h4>
    </div>
</div>

<?php } ?>

<script type="text/javascript">
$('.qty-plus').on('click', function() {
    var $qty = $(this).siblings(".qty-adj");
    var currentVal = parseInt($qty.val());
    if (!isNaN(currentVal)) {
        $qty.val(currentVal + 1);
    }
});
$('.qty-minus').on('click', function() {
    var $qty = $(this).siblings(".qty-adj");
    var _val = $($qty).val();
    var currentVal = parseInt($qty.val());
    if (!isNaN(currentVal) && currentVal > 0) {
        $qty.val(currentVal - 1);
    }
});
</script>