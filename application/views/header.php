<?php if (! isset($selected_menu)): 
	$selected_menu = ''; 
endif ?>

<ul id="nav-menu"><!--
	Home--><li class="menu-left"><a href="<?php echo site_url() ?>" <?php echo ($selected_menu == 'Home' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_home') ?></a></li><!--
	About--><li class="menu-left"><a href="#" 
		<?php echo ($selected_menu == 'About' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_about') ?></a></li><!--
	Help--><li class="menu-left"><a href="#" 
		<?php echo ($selected_menu == 'Help' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_help') ?></a></li><!--
	
	Register--><li class="menu-right"><a href="#" 
		<?php echo ($selected_menu == 'Register' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_register') ?></a></li><!--
	Log In--><li class="menu-right"><a href="#" 
		<?php echo ($selected_menu == 'Log In' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_log_in') ?></a></li>
</ul>

<div id="header">
	<!-- TODO: resize logo image-->
	<a href="<?php site_url() ?>" id="logo"><img src="<?php echo site_url('img/p2p-next--big.png') ?>" alt="P2P-Next" width="119" height="48" /></a>
	<form id="quick-search">
		<label for="quick-search-box"><?php echo $this->lang->line('ui_search') . ': ' ?></label>
		<input type="text" id="quick-search-box" name="quick-search-box" disabled="disabled" value="not yet implemented" />
	</form>
</div>
