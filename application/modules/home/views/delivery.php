<!-- thank-you section start -->
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>Đơn hàng đang được thực hiện</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->

<!-- pricing banner start -->
<section class="pricing-table1 mb-5">
    <div class="custom-container">
        <div class="row">
            <?php if($orders) { ?>
            <?php foreach($orders as $v){ ?>
            <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                <div class="pricing-box mb-3">
                    <div class="pricing-header">
                        <h2>#<?=$v->orderId;?></h2>
                    </div>
                    <div class="pricing-body">
                        <h1 class="py-3 fs-2"><?=number_format($v->grandtotal);?></h1>
                        <?php $products = unserialize($v->detailcart);?>
                        <ul>
                            <?php foreach($products as $p){ ?>
                            <li><a href="javascript:void(0)" class="d-flex flex-column">
                                    <p class="fw-bold">
                                        <?=$p->name;?><?php if ($p->size != '') { echo "(".$p->size.")";}?></p>

                                    <?php if ($p->topping != '') { echo "<span class='fst-italic'>".$p->topping."</span>";}?>
                                    <span class='fst-italic'>SL: <?=$p->amount ?></span>
                                </a> </li>
                            <?php } ?>
                        </ul>
                        <a href="javascript:void(0)" class="btn btn-rounded btn-block" id="fulfillmentOrder<?=$v->id;?>"
                            onclick="updateFulfillmentOrder(<?=$v->id;?>)">Hoàn Thành</a>
                    </div>
                </div>
            </div>
            <?php } ?>
            <?php }else { ?>
            <!--title start-->
            <div class="title6">
                <h4>Tất cả đơn hàng đã được xử lý</h4>
            </div>
            <!--title end-->

            <?php } ?>
        </div>
    </div>
</section>
<!-- pricing banner end -->