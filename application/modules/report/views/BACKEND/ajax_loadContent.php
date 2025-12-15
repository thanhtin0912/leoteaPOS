<script type="text/javascript">
token_value = '<?=$this->security->get_csrf_hash()?>';
</script>
<?php if($price){ ?>
<div class="portlet-title">
	<div class="caption font-green-sharp">
		<span class="caption-subject bold font-red-flamingo uppercase">Tổng tiền:
			<span class="fa-2x"><?=number_format($price);?></span></span>
	</div>
</div>
<hr>
<?php } ?>
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
                    <th class="table-checkbox sorting_disabled" width="25"><input type="checkbox" id="selectAllItems"
                            onclick="selectAllItems(<?= (is_array($result) ? count($result) : 0) ?>)"></th>
                    <th class="center sorting_disabled" width="35">No.</th>
                    <th class="sorting" width="150" onclick="sort('orderId')" id="orderId">Mã order</th>
                    <th class="sorting" onclick="sort('storeName')" id="storeName">Cửa hàng</th>
                    <th class="sorting" onclick="sort('phone')" id="phone">Tài khoản</th>
                    <th class="sorting" onclick="sort('grandtotal')" id="grandtotal">Tổng tiền</th>
                    <th class="sorting" onclick="sort('shipping')" id="shipping">Vận chuyển</th>
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
                    <td><input type="checkbox" id="item<?=$i?>" onclick="selectItem(<?=$i?>)" value="<?=$v->id?>"></td>
                    <td class="center"><?=$k+1+$start?></td>
                    <td><a href="<?=PATH_URL_ADMIN.$module.'/update/'.$v->id?>"><?= ($v->orderId); ?></a></td>
                    <td><a href="<?=PATH_URL_ADMIN.$module.'/update/'.$v->id?>"><?= ($v->storeName); ?></a></td>
                    <td><a href="<?=PATH_URL_ADMIN.$module.'/update/'.$v->id?>"><?= ($v->phone); ?></a></td>
                    <td><a href="<?=PATH_URL_ADMIN.$module.'/update/'.$v->id?>"><?= number_format($v->grandtotal); ?></a></td>
                    <td><a href="<?=PATH_URL_ADMIN.$module.'/update/'.$v->id?>">
                            <?php
					if($v->shipping == 1) {
						echo 'Tại quán.';
					}
					if($v->shipping == 2) {
						echo 'Mang về.';
					}
					if($v->shipping == 3) {
						echo 'Giao hàng.';
					}
					?></a>
                    </td>
                    <td class="center"><?=date('Y-m-d H:i:s',strtotime($v->created))?></td>
                </tr>
                <?php $i++;}
				else{?>
                <tr style="background:#c6c6c6;" class="item_row<?=$i?> gradeX" role="row">
                    <td><input type="checkbox" id="item<?=$i?>" onclick="selectItem(<?=$i?>)" value="<?=$v->id?>"></td>
                    <td class="center"><?=$k+1+$start?></td>
                    <td><?= ($v->orderId); ?></td>
                    <td><?= ($v->storeName); ?></td>
                    <td><?= ($v->phone); ?></td>
                    <td><?= number_format($v->grandtotal); ?></td>
                    <td><?php
						if($v->shipping == 1) {
							echo 'Tại quán.';
						}
						if($v->shipping == 2) {
							echo 'Mang về.';
						}
						if($v->shipping == 3) {
							echo 'Giao hàng.';
						}
						?></td>
                    <td class="center" id="loadStatusID_<?=$v->id?>"><span
                            class="label label-sm label-default status-deleted">Deleted</span></td>
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