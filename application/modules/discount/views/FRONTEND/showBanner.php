<?php if($list_banners): ?>
	<!-- start banner -->
	<div class="header_media">
		<div class="theme_slider_wrapper theme_slider_extended carousel  slide" data-ride="carousel" data-interval="5000" id="estate-carousel">
			<div class="carousel-inner" role="listbox">
				<?php foreach ($list_banners as $key => $banner_item) : ?>
					<div class="item <?= $key == 0 ? 'active' : '' ?>">
						<a class="theme_slider_slide" href="<?= $banner_item->url; ?>"> 
							<img width="1920" height="750" src="<?= resizeImage(PATH_URL . DIR_UPLOAD_BANNER . $banner_item->image, 1920, 750); ?>" class="img-responsive wp-post-image" alt="A palace to call your home." />
						</a>
						<!-- <div class="slider-content-wrapper">  
							<div class="slider-content">
								<h3><a href="<?= $banner_item->url; ?>"><?= $banner_item->title; ?></a></h3>
								<span><?= CutText($banner_item->description, 350); ?><a href="<?= $banner_item->url; ?>" class="read_more">Xem chi tiết<i class="fa fa-angle-right"></i></a></span>
								<a class="carousel-control-theme-next" href="#estate-carousel" data-slide="next"><i class="fa fa-angle-right"></i></a>
								<a class="carousel-control-theme-prev" href="#estate-carousel" data-slide="prev"><i class="fa fa-angle-left"></i></a>
							</div> 
						</div> -->
					</div>
				<?php endforeach; ?>
			</div>
			<ol class="carousel-indicators">
				<?php foreach ($list_banners as $key => $banner_item) : ?>
					<li data-target="#estate-carousel" data-slide-to="<?= $key; ?>" class="<?= $key == 0 ? 'active' : ''; ?>"></li>
				<?php endforeach; ?>
			</ol>
		</div>    
	</div>
	<!-- end banner -->
<?php endif; ?>
