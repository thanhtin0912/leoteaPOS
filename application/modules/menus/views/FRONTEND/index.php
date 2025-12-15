<?php if($list_menu): ?>
	<div class="menu-main-menu-demo-2-container">
		<ul id="menu-main-menu-demo-2" class="menu">
			<?php foreach ($list_menu as $key => $menu) : ?>
				<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-has-children menu-item-5467"><a href="<?= $menu->url; ?>"><?= $menu->name; ?></a>
				<?php if($menu->list_child): ?>
					<ul class="sub-menu">
						<?php foreach ($menu->list_child as $key_child => $child):?>
			    			<li class="menu-item menu-item-type-custom menu-item-object-custom menu-item-5486"><a href="<?= $child->url; ?>"><?= $child->name; ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>       
<?php endif; ?>
