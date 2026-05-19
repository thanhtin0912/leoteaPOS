<?php if($count!= 0){?>
<a href="javascript:void(0)" class="overlay" onclick="closeWishlist()"></a>
<div class="cart-inner">
    <div class="cart_top">
        <h3 class="pl-2">Đơn hàng tạm</h3>
        <div class="close-cart">
            <a href="javascript:void(0)" onclick="closeWishlist()">
                <i class="fa fa-times" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="cart_media">
        <ul class="cart_product">  
            <?php foreach ($orders as $key => $v): ?>
            <li>
                <div class="media">
                    <div class="media-body" href="javascript:void(0)" onclick="getForWaiting('<?=$v['hold_id'] ?>')">
                        <h4><?=$v['hold_time'] ?></h4>
                        <h6>
                            Tổng tiền đơn: <?=number_format($v['totalPrice']) ?>
                        </h6>
                        <span>
                            Tổng Số ly: <?=number_format($v['totalAmount']) ?>
                        </span>
                    </div>
                </div>
            </li>
            <?php endforeach ?>
        </ul>
    </div>
</div>
<?php } else { ?>
<a href="javascript:void(0)" class="overlay" onclick="closeWishlist()"></a>
<div class="cart-inner">
    <div class="cart_top">
        <h3 class="pl-2">Đơn hàng tạm</h3>
        <div class="close-cart">
            <a href="javascript:void(0)" onclick="closeWishlist()">
                <i class="fa fa-times" aria-hidden="true"></i>
            </a>
        </div>
    </div>
    <div class="cart_media border-0">
        <h4 class="text-center">Không có đơn hàng tạm nào</h4>
    </div>
</div>

<?php } ?>