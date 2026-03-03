<style>
.fw {
    font-weight: 600;
}
</style>
<!-- BEGIN PAGE HEADER-->
<h3 class="page-title"><?=$this->session->userdata('Name_Module')?></h3>
<div class="page-bar">
    <ul class="page-breadcrumb">
        <li><i class="fa fa-home"></i><a href="<?=PATH_URL_ADMIN?>">Home</a><i class="fa fa-angle-right"></i></li>
        <li><a href="<?=PATH_URL_ADMIN.$module?>"><?=$this->session->userdata('Name_Module')?></a><i
                class="fa fa-angle-right"></i></li>
        <li><?php ($this->uri->segment(4)=='') ? print 'Add new' : print 'Edit' ?></li>
    </ul>
</div>
<!-- END PAGE HEADER-->
<!-- BEGIN PAGE CONTENT-->
<div class="row">
    <div class="col-lg-12 col-xs-12 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-share font-red-sunglo hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Thông tin cửa hàng</span>
                </div>
            </div>
            <div class="portlet-body">
                <div style="margin: 20px 0 10px 30px">
                    <div class="row">
                        <div class="col-md-2 col-sm-2 col-xs-6 text-stat">
                            <span class="label label-sm label-success fw"> Cửa hàng: </span>
                            <h3 class="fw"><?= $result->storeName ?></h3>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-6 text-stat">
                            <span class="label label-sm label-info fw"> Tài khoản: </span>
                            <h3 class="fw"><?= $result->user ?></h3>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-6 text-stat">
                            <span class="label label-sm label-danger fw"> Tổng tiền bán: </span>
                            <h3 class="fw"><?= number_format($result->sales) ?></h3>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-6 text-stat">
                            <span class="label label-sm label-warning fw"> Tổng tiền nộp: </span>
                            <h3 class="fw"><?= number_format($result->actual) ?></h3>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-6 text-stat">
                            <span class="label label-sm label-info fw"> Chi phí: </span>
                            <h3 class="fw"><?= number_format($result->spent) ?></h3>
                        </div>
                        <div class="col-md-2 col-sm-2 col-xs-6 text-stat">
                            <span class="label label-sm label-danger fw"> Tiền típ: </span>
                            <h3 class="fw"><?= number_format($result->tip) ?></h3>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12">
                            <div class="portlet-title margin-top-20">
                                <div class="caption font-green-sharp">
                                    <span class="caption-subject bold font-red-flamingo uppercase">Tổng chênh lệch:
                                        <span class="fa-2x"><?= number_format($result->diff) ?></span></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Thông tin nhân viên</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="col-md-12 col-sm-12 col-xs-12 text-stat mb-3 margin-bottom-15">
                    <span>Tên nhân viên: <span class="label label-sm label-success fw ">
                            <?= $result->name ?></span></span>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-stat">
                    <span class="label label-sm label-info fw"> Vào ca: </span>
                    <h5 class="fw"><?= $result->from ?></5>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 text-stat">
                    <span class="label label-sm label-danger fw"> Kết ca: </span>
                    <h5 class="fw"><?= ($result->to) ?></h5>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>
    <div class="col-lg-6 col-xs-12 col-sm-12">
        <!-- BEGIN PORTLET-->
        <div class="portlet light bordered">
            <div class="portlet-title">
                <div class="caption">
                    <i class="icon-bar-chart font-dark hide"></i>
                    <span class="caption-subject font-dark bold uppercase">Thông tin tiền nộp</span>
                </div>
            </div>
            <div class="portlet-body">
                <div class="table-scrollable table-scrollable-borderless">
                    <table class="table table-hover table-light">
                        <thead>
                            <tr class="uppercase">
                                <th> Mệnh giá </th>
                                <th> Số tờ </th>
                                <th></th>
                            </tr>
                        </thead>
						<?php $cashs = unserialize($result->report) ?>
                        <tbody>
							<?php if($cashs) { ?>
								<?php foreach ($cashs as $key => $v) { ?>
								<tr>
									<td> <?= number_format($key) ?> </td>
									<td> <?= ($v) ?> </td>
									<td> <?= number_format($v*$key) ?> </td>
								</tr>
								<?php } ?>

							<?php } ?> 

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END PORTLET-->
    </div>

</div>
<!-- END PAGE CONTENT-->