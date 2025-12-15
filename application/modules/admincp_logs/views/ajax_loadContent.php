<script type="text/javascript">token_value = '<?=$this->security->get_csrf_hash()?>';</script>
<div class="dataTables_wrapper no-footer">
	<?php if($result){ ?>
	<div class="row">
		<div class="col-md-5 col-sm-12">
			<?php if(($start+$per_page)<$total){ ?>
			<div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to <?=$start+$per_page?> of <?=$total?> entries</div>
			<?php }else{ ?>
			<div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to <?=$total?> of <?=$total?> entries</div>
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
					<th class="sorting" onclick="sort('function')" id="function" width="110">Module</th>
					<th class="center sorting_disabled" width="50">ID</th>
					<th class="center sorting" onclick="sort('field')" id="field" width="35">Field</th>
					<th class="center sorting_disabled" width="60">Type</th>
					<th class="sorting_disabled">Old Value</th>
					<th class="sorting_disabled">New Value</th>
					<th class="center sorting" onclick="sort('account')" id="account" width="80">Account</th>
					<th class="center sorting" onclick="sort('ip')" id="ip" width="100">IP</th>
					<th class="center sorting" onclick="sort('created')" id="created" width="70">Time</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if($result){
						$i=0;
						$md_f = 'function';
						foreach($result as $k=>$v){
				?>
				<tr class="item_row<?=$i?> gradeX <?php ($k%2==0) ? print 'odd' : print 'even' ?>" role="row">
					<td class="center"><?=$k+1+$start?></td>
					<td><?=$v->$md_f?></td>
					<td class="center"><?=$v->function_id?></td>
					<td class="center"><?=$v->field?></td>
					<td class="center"><?=$v->type?></td>
					<td><?= cutText($v->old_value, 20); ?></td>
					<td><?= cutText($v->new_value, 20); ?></td>
					<td class="center"><?=$v->account?></td>
					<td class="center"><?=$v->ip?></td>
					<td class="center"><?=date('Y-m-d H:i:s',strtotime($v->created))?>
					</td>
				</tr>
				<?php $i++;}}else{ ?>
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
			<div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to <?=$start+$per_page?> of <?=$total?> entries</div>
			<?php }else{ ?>
			<div class="dataTables_info" style="padding-left:0;margin-top:3px">Showing <?=$start+1?> to <?=$total?> of <?=$total?> entries</div>
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