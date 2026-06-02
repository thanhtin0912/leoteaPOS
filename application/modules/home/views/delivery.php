<!-- thank-you section start -->
<section class="section-big-py-space light-layout">
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="success-text">
                    <h2>Danh sách</h2>
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
            <h4>Danh sách bill chờ hủy</h4>
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
            <div class="col-sm-12">
                <table class="table cart-table table-responsive-xs">
                    <thead>
                        <tr class="table-head">
                            <th scope="col">TN</th>
                            <th scope="col">Số Ly</th>
                            <th scope="col">Chi tiết</th>
                        </tr>
                    </thead>

                    <?php foreach($banking as $v){ ?>
                    <tbody>
                        <tr>
                            <td>
                                <h4>#<?=$v->fullname;?></h4>
                            </td>
                            <td>
                                <?php 
                                    $finalamount=0;
                                    $products = unserialize($v->detailcart);
                                    foreach($products as $p){
                                        $finalamount += $p->amount;
                                    } 
                                ?>
                                <span>SL: <?=$finalamount ?></span>
                            </td>
                            <td>
                                <div class="responsive-data">
                                    <h4 class="price"><?=number_format($v->grandtotal);?></h4>
                                </div>
                                <span class="dark-data"></span><?=$v->created ?>
                            </td>
                        </tr>
                    </tbody>

            <?php } ?>
                </table>
            </div>

        </div>

        <?php } ?>
    </div>
</section>
<!-- pricing banner end -->