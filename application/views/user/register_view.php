<?php echo form_open("user/register/$redirect") ?>
<table class="form">
	<tr>
		<td class="form-header"></td>
		<td><span class="required"><?php echo $this->lang->line('user_note_required_fields') ?></span></td>
	</tr>
	
	<tr><td></td><td>&nbsp;</td></tr>

	<tr>
		<th><?php echo $this->lang->line('user_username'). ' <span class="required">*</span> ' ?></th>
		<td>
			<input type="text" name="username" size="16" value="<?php echo set_value('username') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('username') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_password'). ' <span class="required">*</span> ' ?></th>
		<td>
			<input type="password" name="password" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('password') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_password_confirmation'). ' <span class="required">*</span> ' ?></th>
		<td>
			<input type="password" name="password-confirmation" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('password-confirmation') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_email'). ' <span class="required">*</span> ' ?></th>
		<td>
			<input type="text" name="email" size="16" value="<?php echo set_value('email') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('email') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_first_name'). ' <span class="required">*</span> ' ?></th>
		<td>
			<input type="text" name="first-name" size="16" value="<?php echo set_value('first-name') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('first-name') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_last_name'). ' <span class="required">*</span> ' ?></th>
		<td>
			<input type="text" name="last-name" size="16" value="<?php echo set_value('last-name') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('last-name') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_birth_date'). ' &nbsp;' ?></th>
		<td>
			<input type="text" name="birth-date" id="birth-date" size="16" value="<?php echo set_value('birth-date') ?>" /> (<?php echo $this->lang->line('user_date_format_hint') ?>)
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('birth-date') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_country'). ' <span class="required">*</span> ' ?></th>
		<td>
			<?php echo country_dropdown('country', array('RO'), set_value('country', 'RO')) ?>
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('country') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_locality'). ' &nbsp;' ?></th>
		<td>
			<input type="text" name="locality" size="16" value="<?php echo set_value('locality') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('locality') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_ui_lang'). ' &nbsp;' ?></th>
		<td>
			<?php echo available_languages_dropdown('ui-lang', set_value('ui-lang', 'en')) ?>
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('ui-lang') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_time_zone'). ' <span class="required">*</span> ' ?></th>
		<td>
			<?php echo timezone_menu(set_value('time-zone', 'UP2')) ?>
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('time-zone') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<td></td>
		<td>
			<input type="submit" value="<?php echo $this->lang->line('ui_nav_menu_register') ?>" />
		</td>
	</tr>
</table>
</form>

<script type="text/javascript">
	$(function() {
		$( "#birth-date" ).datepicker({
			dateFormat: "yy-mm-dd",
			changeMonth: true,
			changeYear: true,
			yearRange: '1910:2011',
			showOn: "both",
			buttonImage: "<?php echo site_url('img/calendar.gif') ?>",
			buttonImageOnly: true
		});
	});
</script>