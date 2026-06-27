<script type="text/javascript">token_value = '<?=$this->security->get_csrf_hash()?>';</script>
<div class="dataTables_wrapper no-footer">
	<?php if($result){ ?>
	<div class="row">
	</div>
	<?php } ?>
	<div class="table-scrollable">
		<table class="table table-striped table-bordered table-hover dataTable no-footer">
			<thead>
				<tr role="row">
					<th class="center sorting_disabled" width="35">No.</th>
					<th class="sorting" onclick="sort('title')" id="title">Size Name</th>
					<th class="center sorting" width="100">Tổng Ly</th>
				</tr>
			</thead>
			<tbody>
				<?php
					if($result){
						$i=0;
						foreach($result as $k=>$v){
				?>
				<tr class="item_row<?=$i?> gradeX <?php ($k%2==0) ? print 'odd' : print 'even' ?>" role="row">
					<td class="center"><?=$k+1+$start?></td>
					<td><b><?= $v->name; ?></b></td>
					<td class="center"><?= number_format($v->total_cup); ?></td>
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
			<div class="dataTables_info" style="padding-left:0;margin-top:3px"><strong>Tổng Order: <?= number_format($total) ?> entries</strong></div>
		</div>
	</div>
	<?php } ?>
</div>