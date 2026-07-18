<script type="text/javascript">
token_value = '<?=$this->security->get_csrf_hash()?>';
</script>

<div class="dataTables_wrapper no-footer">
    <?php if($result){ ?>
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <?php if(($start+$per_page)<$total){ ?>
            <div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to
                <?=$start+$per_page?> of <?=$total?> entries</div>
            <?php }else{ ?>
            <div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to <?=$total?> of
                <?=$total?> entries</div>
            <?php } ?>
        </div>

        <div class="col-md-7 col-sm-12">
            <div class="dataTables_paginate paging_bootstrap_full_number" style="margin-top:3px">
                <ul class="pagination" style="visibility: visible;">
                    <?=$this->adminpagination->create_links();?>
                </ul>
            </div>
        </div>
    </div>
    <?php } ?>
    <div class="table-scrollable">
        <table class="table table-striped table-bordered table-hover dataTable no-footer">
            <thead>
                <tr role="row">
                    <th class="center sorting_disabled" width="35">No.</th>
                    <th class="sorting" width="150">Xác nhận</th>
                    <th class="sorting" width="150" onclick="sort('store')" id="store">Cửa hàng</th>
                    <th class="sorting" onclick="sort('user')" id="user">Tài khoản</th>
                    <th class="sorting" onclick="sort('name')" id="name">Nhân viên</th>
                    <th class="sorting">Tổng tiền</th>
                    <th class="sorting" onclick="sort('shipping')" id="shipping">Size Ly</th>
                    <th class="sorting">Ghi chú</th>
                    <th class="center sorting" width="80" onclick="sort('created')" id="created">Created</th>
                </tr>
            </thead>
            <tbody>
                <?php
					if($result){
						$i=0;
						foreach($result as $k=>$v){
							if($v->delete==0){
				?>
                <tr class="item_row<?=$i?> gradeX <?php ($k%2==0) ? print 'odd' : print 'even' ?>" role="row">
                    <td class="center"><?=$k+1+$start?></td>
                    <td class="center" id="loadStatusID_<?=$v->id?>"><?php ($v->isVerify==0) ? print '<a class="no_underline" href="javascript:void(0)" onclick="updateStatus('.$v->id.','.$v->isVerify.',\''.$module.'\')"><span class="label label-sm label-default status-blocked">Chờ xác nhận</span></a>' : print '<span class="label label-sm label-success status-approved">Đã xác nhận</span>' ?></td>
                    <td><?= ($v->storeName); ?></td>
                    <td><?= ($v->user); ?></td>
                    <td><?= ($v->name); ?></td>
                    <td><?= number_format($v->amount_price); ?></td>
                    <td><?php $amount_sizes = unserialize($v->amount_sizes); if(is_array($amount_sizes)) { foreach($amount_sizes as $size) { echo $size->name . ': ' . $size->in . '<br>'; } } ?></td>
                    <td><?= ($v->note); ?></td>
                    <td class="center"><?=date('Y-m-d H:i:s',strtotime($v->created))?></td>
                </tr>
                <?php $i++;}
				}}else{ ?>
                <tr class="gradeX odd" role="row">
                    <td class="center no-record" colspan="20">No record</td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php if($result){ ?>
    <div class="row">
        <div class="col-md-5 col-sm-12">
            <?php if(($start+$per_page)<$total){ ?>
            <div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to
                <?=$start+$per_page?> of <?=$total?> entries</div>
            <?php }else{ ?>
            <div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to <?=$total?> of
                <?=$total?> entries</div>
            <?php } ?>
        </div>

        <div class="col-md-7 col-sm-12">
            <div class="dataTables_paginate paging_bootstrap_full_number" style="margin-top:3px">
                <ul class="pagination" style="visibility: visible;">
                    <?=$this->adminpagination->create_links();?>
                </ul>
            </div>
        </div>
    </div>
    <?php } ?>
</div>