<input type="hidden" id="hdshowData" value="2">
<label class="btn btn-circle btn-transparent grey-salsa <?= $target == 2 ? 'active' : ''; ?> " name="lbShowData" onclick="showData(this)" value="2">
    <input type="radio" class="toggle" value="2">All (<?=$totalAll?>)
</label>
<label class="btn btn-circle btn-transparent grey-salsa <?= $target == 0 ? 'active' : ''; ?> " name="lbShowData" onclick="showData(this)" value="0">
    <input type="radio" class="toggle" value="0">Publish (<?=$totalPublish?>)
</label>
<label class="btn btn-circle btn-transparent grey-salsa <?= $target == 1 ? 'active' : ''; ?> " name="lbShowData" onclick="showData(this)" value="1">
    <input type="radio" class="toggle" value="1">Trash (<?=($totalAll-$totalPublish)?>)
</label>
<script type="text/javascript">
	$(document).ready(function(){
		var count = '<?php echo $totalAll-$totalPublish?>';
		if(count > 0){
			$('.restore').css('display','inline-block');
		}else{
			$('.restore').css('display','none');
		}
	});
</script>