<?php if (! isset($selected_menu)): 
	$selected_menu = ''; 
endif ?>

<ul id="nav-menu">
	<li class="menu-left"><a href="<?php echo site_url() ?>"
		<?php echo ($selected_menu == 'home' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_home') ?></a></li>
	
	<li class="menu-left"><a href="<?php echo site_url('install-plugins') ?>"
		<?php echo ($selected_menu == 'install-plugins' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_install_plugins') ?></a></li>
	
	<li class="menu-left"><a href="<?php echo site_url('about') ?>" 
		<?php echo ($selected_menu == 'about' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_about') ?></a></li>
	
	<li class="menu-left"><a href="<?php echo site_url('help') ?>" 
		<?php echo ($selected_menu == 'help' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_help') ?></a></li>
		
	<li class="menu-left"><a href="<?php echo site_url('contact') ?>" 
		<?php echo ($selected_menu == 'contact' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_contact') ?></a></li>
	
	<li class="menu-right"><a href="#<?php //echo site_url('register') ?>" 
		<?php echo ($selected_menu == 'register' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_register') ?></a></li>
		
	<li class="menu-right"><a href="#<?php //echo site_url('login') ?>" 
		<?php echo ($selected_menu == 'login' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_log_in') ?></a></li>
</ul>

<div id="header">
	<!-- TODO: resize logo image-->
	<a href="<?php echo site_url() ?>" id="logo"><img src="<?php echo site_url('img/p2p-next--big.png') ?>" alt="P2P-Next" width="119" height="48" /></a>
	<form id="quick-search">
		<label for="quick-search-box"><?php echo $this->lang->line('ui_search') . ': ' ?></label>
		<input type="text" id="quick-search-box" name="quick-search-box" disabled="disabled" value="not yet implemented" />
	</form>
</div>