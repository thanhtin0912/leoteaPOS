<!-- thank-you section start -->
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>Lịch sử đơn hàng</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->
<!--order tracking start-->
<section class="order-tracking section-big-my-space my-5 mb-5">
  <div class="container" >
    <div class="row">
    <div class="col-12">
        <div class="order-payment order-tracking-box">
        <div class="accordion theme-accordion" id="accordionOrderToday">
            <?php if($orderToday) { ?>
            <?php $totalPriceTimeSheet = 0 ?>  
            <?php foreach($orderToday as $key =>$v){ ?>
            <?php
                $time1 = new DateTime($v->created);
                $time2 = new DateTime($v->updated);
                // Tính hiệu giữa 2 thời điểm
                $interval = $time1->diff($time2);
            ?>
            <div class="card">
                <div class="card-header" id="headingOne">
                    <button class="btn btn-link collapsed payment-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?=$v->id ?>" >
                        <div class="d-flex justify-content-between">
                            <span><?=$key+1 ?></span>
                            <?php if($v->updated) { ?>
                                <span><?=$interval->format('%i phút %s giây')?></span>
                            <?php } else { ?>
                                <span>Chưa hoàn thành</span>
                            <?php } ?>
                            <span><?=$v->created ?></span> 
                        </div>
                    </button>
                </div>
                <div id="collapse<?=$v->id ?>" class="collapse paymant-collapce"  data-parent="#accordionOrderToday" style="">
                    <div class="product-order">
                        <?php $products = unserialize($v->detailcart);?>
                        <?php foreach ($products as $key => $c): ?>

                        <div class="row product-order-detail py-2">
                            <div class="col-6 order_detail">
                                <div>
                                    <h4><?= $c->name; ?> <?php if ($c->size != '') { echo "(".$c->size.")";}?></h4>
                                    <?php if($c->priceTopping > 0) { ?>
                                    <h5><?= $c->topping; ?></h5>
                                    <?php } ?>
                                </div>
                            </div>
                            <div class="col-3 order_detail">
                                <div>
                                    <h4>SL</h4>
                                    <h5><?= $c->amount; ?></h5></div>
                            </div>
                            <div class="col-3 order_detail justify-content-end">
                                <div>
                                    <h4>Đơn giá</h4>
                                    <h5><?php echo number_format($c->totalPrice); ?></h5></div>
                            </div>
                        </div>
                        <hr>
                        <?php endforeach ?>
                        <?php $totalPriceTimeSheet = $totalPriceTimeSheet + $v->grandtotal ?> 
                        <div class="final-total pt-2">
                            <!-- <h3>Tổng tiền <span><?= number_format($v->grandtotal); ?></span></h3> -->
                        </div>
                    </div>
                </div>
            </div>
            <?php } ?>
            <div class="title6">
                <h4>Tổng đơn hàng trong ca: <?=count($orderToday)?> </h4>
            </div>
            <?php }else { ?>
            <!--title start-->
            <div class="title6">
                <h4>Chưa có đơn hàng hôm nay.</h4>
            </div>
            <!--title end-->
            <?php } ?>
        </div>
        </div>
    </div>
    </div>
  </div>
</section>
<!--order tracking end-->