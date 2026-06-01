<!-- thank-you section start -->
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>Danh sách hóa đơn</h2>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- Section ends -->


<!-- pricing banner start -->
<section class="pricing-table1 mb-5">
    <div class="custom-container">
        <?php if($orders) { ?>
        <div class="title6">
            <h4>Danh sách hóa đơn chờ hủy</h4>
        </div>
        <div class="row">
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
                        <a href="javascript:void(0)" class="btn btn-rounded btn-block" disabled>Chờ hủy</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
         <?php } ?>
    </div>
</section>
<!-- pricing banner end -->
 <!-- pricing banner start -->
<section class="pricing-table1 mb-5">
    <div class="custom-container">
        <?php if($banking) { ?>
        <?php
            // Tính tổng trước khi hiển thị
            $banking_total = 0;
            foreach($banking as $v){
                $banking_total += $v->grandtotal;
            }
        ?>
        <div class="title6">
            <h4>Tổng Ck: <?=number_format($banking_total);?></h4>
        </div>
        <div class="row">

            <?php foreach($banking as $v){ ?>
            <div class="col-xl-3 col-lg-3 col-md-3 col-12">
                <div class="pricing-box mb-3">
                    <div class="pricing-header">
                        <h2>#<?=$v->orderId;?>....</h2>
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
                        <a href="javascript:void(0)" class="btn btn-rounded btn-block" disabled>TT: CK</a>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>

        <?php } ?>
    </div>
</section>
<!-- pricing banner end -->