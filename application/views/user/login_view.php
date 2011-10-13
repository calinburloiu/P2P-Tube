<?php echo form_open("user/login/$redirect") ?>
<fieldset>
	<legend><?php echo sprintf($this->lang->line('user_legend_login_normal'), $this->config->item('site_name')) ?></legend>
	
	<table class="form">
		<tr>
			<th><?php echo $this->lang->line('user_username_or_email'). ': ' ?></th>
			<td>
				<input type="text" name="username" size="16" value="<?php echo set_value('username') ?>" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo form_error('username') ?></td>
		</tr>
		<tr>
			<th><?php echo $this->lang->line('user_password'). ': ' ?></th>
			<td>
				<input type="password" name="password" size="16" value="" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo form_error('password') ?></td>
		</tr>
		<tr>
			<td></td>
			<td>
				<input type="submit" value="<?php echo $this->lang->line('ui_nav_menu_log_in') ?>" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td>
				<p><a href="<?php echo site_url('user/recover_password') ?>"><?php echo $this->lang->line('user_link_password_recovery') ?></a></p>
			</td>
		</tr>
	</table>
</fieldset>
</form>

<?php echo form_open("user/login/$redirect") ?>
<fieldset>
	<legend><?php echo $this->lang->line('user_legend_login_openid') ?></legend>
	
	<table class="form">
		<tr>
			<th><?php echo $this->lang->line('user_openid'). ': ' ?></th>
			<td>
				<input type="text" name="openid" size="64" value="<?php echo set_value('openid') ?>" />
			</td>
		</tr>
		<tr>
			<td></td>
			<td><?php echo form_error('openid') ?></td>
		</tr>
		
		<tr>
			<td></td>
			<td>
				<input type="submit" value="<?php echo $this->lang->line('ui_nav_menu_log_in') ?>" />
			</td>
		</tr>
	</table>
</fieldset>
</form>