<table>
	<tr>
		<th><?php echo $this->lang->line('user_username_or_email'). ': ' ?></th>
		<td>
			<input type="text" name="username" size="32" />
		</td>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('user_password'). ': ' ?></th>
		<td>
			<input type="password" name="password" size="32" />
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="submit" value="<?php echo $this->lang->line('ui_nav_menu_log_in') ?>" />
		</td>
	</tr>
</table>