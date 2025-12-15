<script type="text/javascript">

</script>

<!-- BEGIN PAGE HEADER-->
<h3 class="page-title"><?=$this->session->userdata('Name_Module')?></h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><i class="fa fa-home"></i><a href="<?=PATH_URL_ADMIN?>">Home</a><i class="fa fa-angle-right"></i></li>
        <li><a href="<?=PATH_URL_ADMIN.$module?>"><?=$this->session->userdata('Name_Module')?></a><i
                class="fa fa-angle-right"></i></li>
        <li><?php ($this->uri->segment(4)=='') ? print 'Add new' : print 'Chi tiết đơn hàng' ?></li>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
    <div class="col-xs-12">
        <!-- BEGIN EXAMPLE TABLE PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-paper-plane font-green-haze"></i>
                    <span class="caption-subject bold font-green-haze uppercase">Chi tiết đơn hàng</span>
                </div>
            </div>

            <div class="portlet-body form">
                <div class="tab-content">
                    <div class="tab-pane active" id="tab_1">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <div class="portlet green-meadow box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>Thông tin
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="row static-info">
                                            <div class="col-xs-5 name"> Order #: </div>
                                            <div class="col-xs-7 value"> <?=$result->orderId;?>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-xs-5 name"> Ngày tạo: </div>
                                            <div class="col-xs-7 value"> <?=$result->created;?></div>
                                        </div>
										<div class="row static-info">
                                            <div class="col-xs-5 name"> Người tạo: </div>
                                            <div class="col-xs-7 value"> <?=$result->phone;?></div>
                                        </div>
										<div class="row static-info">
                                            <div class="col-xs-5 name"> Cửa hàng: </div>
                                            <div class="col-xs-7 value"> <?=$result->storeName;?></div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-xs-5 name"> Giao hàng: </div>
                                            <div class="col-xs-7 value">
                                                <span class="label label-success"> 
												<?php if($result->shipping == 1) {
													echo 'Tại quán';
												}
												if($result->shipping == 2) {
													echo 'Mang về';
												}
												if($result->shipping == 3) {
													echo 'Giao hàng';
												} ?>
												</span>
                                            </div>
                                        </div>
                                        <div class="row static-info">
                                            <div class="col-xs-5 name"> Tổng tiền: </div>
                                            <div class="col-xs-7 value"> <?= number_format($result->grandtotal)?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12">
                                <div class="portlet grey-cascade box">
                                    <div class="portlet-title">
                                        <div class="caption">
                                            <i class="fa fa-cogs"></i>Chi tiết đơn hàng
                                        </div>
                                    </div>
                                    <div class="portlet-body">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered table-striped">
                                                <thead>
                                                    <tr>
                                                        <th> Tên sản phẩm </th>
                                                        <th> Size </th>
                                                        <th> Topping </th>
                                                        <th> Sổng tiền topping </th>
                                                        <th> SL </th>
                                                        <th> Thành tiền </th>
                                                    </tr>
                                                </thead>
												<?php $products = unserialize($result->detailcart);?>
                        						
                                                <tbody>
													<?php foreach ($products as $key => $c): ?>
                                                    <tr>
                                                        <td>
                                                            <a href="javascript:;"> <?= $c->name?> </a>
                                                        </td>
                                                        <td>
															<?php if($c->size!= '' ) {?>
                                                            <span class="label label-sm label-success"> <?= $c->size?> 
                                                            </span>
															<?php } ?>
                                                        </td>
                                                        <td> <?=($c->priceTopping >0) ? $c->topping :'';?></td>
                                                        <td> <?=($c->priceTopping >0) ? number_format($c->priceTopping) :'';?></td>
                                                        <td> <?= $c->amount?> </td>
                                                        <td> <?= number_format($c->totalPrice)?></td>
                                                    </tr>
													<?php endforeach ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END EXAMPLE TABLE PORTLET-->
    </div>
</div>
<!-- END PAGE CONTENT-->