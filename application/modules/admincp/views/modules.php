<div id="titlefont26">Management Modules</div>

<div class="fl" id="loadingContent">
	<div class="main_content">
		<table width="100%" class="table_content" cellspacing=0 cellpadding=0>
			<tr>
				<th width="40">No.</th>
				<th width="160" align="left" style="padding-left: 10px;">Module name</th>
				<th width="70">Status</th>
				<th width="90" align="left" style="padding-left: 10px;">Created date</th>
			</tr>
			<?php
				if(!empty($result)){
					$i=0;
					foreach($result as $k=>$v){
			?>
			<tr class="td_hover" id="grItem<?=$i?>">
				<td><?=($i+1)?></td>
				<td style="padding: 7px 10px; text-align: left;"><?=$v['name']?></td>
				<td><?php ($v['status']==0) ? print 'Install' : print 'UnInstall' ?></td>
				<td style="padding-left: 10px; text-align: left;"><?=date('d/m/Y',strtotime($v['created']))?></td>
			</tr>
			<?php $i++;}}else{ ?>
			<tr>
				<td colspan="20" class="grNoData">No Data</td>
			</tr>
			<?php } ?>
		</table>
	</div>
</div>
<div class="footer_pagination"></div>