<?php 
	if (! isset($selected_menu))
		$selected_menu = '';
	if (! isset($search_query))
		$search_query = '';
	if (! isset($search_category_name))
		$search_category_name = NULL;
?>

<ul
	id="nav-menu">
	<li class="menu-left"><a href="<?php echo site_url() ?>"


	<?php echo ($selected_menu == 'home' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_home') ?>
	</a></li>

	<li class="menu-left"><a
		href="<?php echo site_url('install-plugins') ?>"


		<?php echo ($selected_menu == 'install-plugins' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_install_plugins') ?>
	</a></li>

	<li class="menu-left"><a href="<?php echo site_url('about') ?>"


	<?php echo ($selected_menu == 'about' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_about') ?>
	</a></li>

	<li class="menu-left"><a href="<?php echo site_url('help') ?>"


	<?php echo ($selected_menu == 'help' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_help') ?>
	</a></li>

	<li class="menu-left"><a href="<?php echo site_url('contact') ?>"


	<?php echo ($selected_menu == 'contact' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_contact') ?>
	</a></li>

	<!--<li class="menu-right"><a href="#<?php //echo site_url('register') ?>"
		<?php echo ($selected_menu == 'register' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_register') ?></a></li>
		
	<li class="menu-right"><a href="#<?php //echo site_url('login') ?>" 
		<?php echo ($selected_menu == 'login' ? 'class="selected"' : '') ?>><?php echo $this->lang->line('ui_nav_menu_log_in') ?></a></li>-->
</ul>

<div id="header">
	<!-- TODO: resize logo image-->
	<a href="<?php echo site_url() ?>" id="logo"><img
		src="<?php echo site_url('img/p2p-next--big.png') ?>" alt="P2P-Next"
		width="119" height="48" /> </a>
	
	
	
	
	<?php echo form_open('catalog/search', array('id'=>'quick-search')); ?>
		<label for="search"><?php 
			if ($search_category_name === NULL)
				echo $this->lang->line('ui_search') . ':';
			else
				echo $this->lang->line('ui_search_in') . ' <em>'
				. $search_category_title . '</em>:';
		?>
		</label>
		<input type="text" id="search" name="search" value="<?php echo $search_query ?>" />
		<input type="submit" id="button-quick-search" value="<?php echo $this->lang->line('ui_search') ?>" />
		<a href="#" id="button-js-quick-search" style="display:none">
			<?php echo $this->lang->line('ui_search') ?>
		</a>
	</form>
</div>

<script type="text/javascript">
	$(function() {
		$('#button-quick-search')
			.hide();

		// Fake JS submit via CI URI segments
		var fakeSubmit = function() {
			var searchQuery = $('#search').val();

			if (searchQuery.length === 0)
			{
				alert('<?php echo $this->lang->line('error_search_query_empty') ?>');
				return;
			}
			
			searchQuery = searchQuery.replace(/\*/g, '_AST_');  // *
			searchQuery = searchQuery.replace(/\+/g, '_AND_');	// +
			//searchQuery = searchQuery.replace(/\-/g, '_');	// -
			searchQuery = searchQuery.replace(/\s/g, '+');	// <white spaces>
			searchQuery = searchQuery.replace(/>/g, '_GT_');	// >
			searchQuery = searchQuery.replace(/\</g, '_LT_');	// <
			searchQuery = searchQuery.replace(/\(/g, '_PO_');	// (
			searchQuery = searchQuery.replace(/\)/g, '_PC_');	// )
			searchQuery = searchQuery.replace(/~/g, '_LOW_');	// ~ 
			searchQuery = searchQuery.replace(/"/g, '_QUO_');	// " 
			searchQuery = encodeURI(searchQuery);
			
			window.location = "<?php echo site_url('catalog/search') ?>/" 
				+ searchQuery + '/0'
				+ "<?php echo ($search_category_name === NULL ? '' : '/'. $search_category_name) ?>";
		};
		
		$('#button-js-quick-search')
			.show()
			.button({
				icons: {
	                primary: "ui-icon-search"
	            },
	            text: false
			})
			.click(function(event) {
				fakeSubmit();
			});

		$('#search')
			.keypress(function(event) {
				if (event.which == 13)
				{
					fakeSubmit();

					event.preventDefault();
					return false;
				}
			});
	});

</script>
