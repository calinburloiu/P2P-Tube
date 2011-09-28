<?php
function _set_value($userdata, $field, $default = '')
{
	$post_value = set_value($field, $default);
	
	if (! $userdata)
		return $post_value;

	return ($post_value === $default 
		? $userdata[ str_replace('-','_',$field) ]
		: $post_value);
}

if (!$userdata)
	echo form_open_multipart("user/register/$redirect");
else
	echo form_open_multipart("user/account/$redirect");
?>

<?php if ($userdata): ?>
<input type="hidden" name="user-id" value="<?php echo $userdata['id'] ?>" />
<input type="hidden" name="username" value="<?php echo $userdata['username'] ?>" />
<?php endif ?>

<table class="form">
	<tr>
		<td></td>
		<td><span class="required"><?php echo $this->lang->line('user_note_required_fields') ?></span></td>
	</tr>
	
	<tr><td></td><td>&nbsp;</td></tr>

	<tr>
	  <?php if (! $userdata): ?>
		<th><?php echo $this->lang->line('user_username'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="text" name="username" size="16" value="<?php echo _set_value($userdata, 'username') ?>" />
		</td>
	  <?php else: ?>
		<th><?php echo $this->lang->line('user_username'). ' : ' ?></th>
		<td>
			&nbsp;<em><?php echo $userdata['username'] ?></em>
		</td>`
	  <?php endif ?>
	</tr>
	<tr><td></td><td><?php echo form_error('username') ?></td></tr>
	
  <?php // Register requires password ?>
  <?php if (! $userdata):?>
	<tr>
		<th><?php echo $this->lang->line('user_password'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="password" name="password" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('password') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_password_confirmation'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="password" name="password-confirmation" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('password-confirmation') ?></td></tr>
  <?php // Edit account data requires password reset ?>
  <?php elseif ($userdata && $userdata['auth_src'] == 'internal'): ?>
	<tr>
		<th><?php echo $this->lang->line('user_old_password'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="password" name="old-password" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('old-password') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_new_password'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="password" name="new-password" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('new-password') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_new_password_confirmation'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="password" name="new-password-confirmation" size="16" value="" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('new-password-confirmation') ?></td></tr>
  <?php endif ?>
	
	<tr>
		<th><?php echo $this->lang->line('user_email'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="text" name="email" size="16" value="<?php echo _set_value($userdata, 'email') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('email') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_first_name'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="text" name="first-name" size="16" value="<?php echo _set_value($userdata, 'first-name') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('first-name') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_last_name'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<input type="text" name="last-name" size="16" value="<?php echo _set_value($userdata, 'last-name') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('last-name') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_birth_date'). ' : ' ?></th>
		<td>
			<input type="text" name="birth-date" id="birth-date" size="16" value="<?php echo _set_value($userdata, 'birth-date') ?>" /> (<?php echo $this->lang->line('user_date_format_hint') ?>)
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('birth-date') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_country'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<?php echo country_dropdown('country', array('RO'), _set_value($userdata, 'country', 'RO')) ?>
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('country') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_locality'). ' : ' ?></th>
		<td>
			<input type="text" name="locality" size="16" value="<?php echo _set_value($userdata, 'locality') ?>" />
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('locality') ?></td></tr>
	
  <?php if ($userdata && $userdata['picture']): ?>
	<tr>
		<th><?php echo $this->lang->line('user_picture'). ' : ' ?></th>
		<td>
			<a href="<?php echo $userdata['picture'] ?>"><img src="<?php echo $userdata['picture_thumb'] ?>" alt="<?php echo $userdata['username'] ?>" /></a>
		</td>
	</tr>
	<tr><td></td><td></td></tr>
  <?php endif ?>
	
	<tr>
		<th>
		  <?php if (! $userdata || ($userdata && ! $userdata['picture'])): ?>
			<?php echo $this->lang->line('user_picture'). ' : ' ?>
		  <?php else: ?>
			<?php echo $this->lang->line('user_change_picture'). ' : ' ?>
		  <?php endif ?>
		</th>
		<td>
			<input type="file" name="picture" size="16" />
		</td>
	</tr>
	<tr><td></td><td><?php echo $error_upload ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_ui_lang'). ' : ' ?></th>
		<td>
			<?php echo available_languages_dropdown('ui-lang', _set_value($userdata, 'ui-lang', 'en')) ?>
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('ui-lang') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('user_time_zone'). ' <span class="required">*</span> : ' ?></th>
		<td>
			<?php echo timezone_menu(_set_value($userdata, 'time-zone', 'UP2'), '', 'time-zone') ?>
		</td>
	</tr>
	<tr><td></td><td><?php echo form_error('time-zone') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<td></td>
		<td>
		  <?php if (! $userdata): ?>
			<input type="submit" value="<?php echo $this->lang->line('user_submit_register') ?>" />
		  <?php else: ?>
			<input type="submit" value="<?php echo $this->lang->line('user_submit_save') ?>" />
		  <?php endif ?>
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