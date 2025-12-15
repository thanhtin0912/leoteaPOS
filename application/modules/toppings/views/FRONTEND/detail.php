<div class="row">
    <div class="col-xs-12 col-md-12 breadcrumb_container"><ol class="breadcrumb">
       <li><a href="<?= PATH_URL; ?>">Trang chủ</a></li><li class="active"><?= $item[0]->title; ?></li></ol>
    </div>
    <div class="col-md-8 rightmargin news_detail">
		<h1><?= $item[0]->title; ?></h1>
		<hr/>
		<div class="single-content listing-content">
			<?= parserEditorHTML($item[0]->content); ?>
		</div>
    </div>
    <div class="clearfix visible-xs"></div>
    <div class="col-xs-12 col-md-4 widget-area-sidebar" id="primary">
    <ul class="xoxo">
        <li id="featured_widget" class="widget-container featured_sidebar">
        	<div class="text-center">
				<img src="<?= PATH_URL; ?>assets/images/frontend/avatar.png" class="img-thumbnail avatar-thumbnail" alt="Mr Toan">
				<h2 style="color: #e53e49;">0902-330-322 (Mr Toàn)</h2>
			</div>
        </li>   
    	<?php if($list_highlight): ?>
        <li id="featured_widget" class="widget-container featured_sidebar">
        	<h3 class="text-center" style="color: #00796B; font-weight: bold;">CĂN HỘ NỔI BẬT</h3>
        	<?php foreach ($list_highlight as $key => $highlight): ?>
        		<div class="property_listing" data-link="<?= PATH_URL ?>du-an/<?= $highlight->slug; ?>">
                    <div class="listing-unit-img-wrapper">
                        <a href="<?= PATH_URL ?>du-an/<?= $highlight->slug; ?>">
                            <img width="525" height="328" src="<?= resizeImage(PATH_URL . DIR_UPLOAD_NEWS . $highlight->image, 525, 328); ?>" class="lazyload img-responsive wp-post-image" alt="Front view of the house." data-original="<?= PATH_URL . DIR_UPLOAD_NEWS . $highlight->image; ?>" />
                        </a>
                        <div class="listing-cover"></div>
                        <a href="<?= PATH_URL ?>du-an/<?= $highlight->slug; ?>"><span class="listing-cover-plus">+</span></a>
                    </div>
                    <h4><a href="<?= PATH_URL ?>du-an/<?= $highlight->slug; ?>"><?= CutText($highlight->title, 40); ?></a></h4> 
                    <div class="listing_details the_grid_view"><?= CutText(strip_tags($highlight->description), 200); ?></div>
                </div>   
        	<?php endforeach; ?>
        </li>  
    	<?php endif; ?>
    </ul>
	
    	
    </div>
</div>
