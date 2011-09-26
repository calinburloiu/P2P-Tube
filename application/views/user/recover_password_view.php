<?php echo form_open("user/recover_password") ?>
<table class="form">
	<tr>
		<td></td>
		<td>
			<p><?php echo $this->lang->line('user_instruction_password_recovery'); ?></p>
		</td>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('user_username'). ': ' ?></th>
		<td>
			<input type="text" name="username" size="24" value="<?php echo set_value('username') ?>" />
		</td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo form_error('username') ?></td>
	</tr>
	<tr>
		<th><?php echo $this->lang->line('user_email'). ': ' ?></th>
		<td>
			<input type="text" name="email" size="24" value="<? echo set_value('email') ?>" />
		</td>
	</tr>
	<tr>
		<td></td>
		<td><?php echo form_error('email') ?></td>
	</tr>
	<tr>
		<td></td>
		<td>
			<input type="submit" value="<?php echo $this->lang->line('user_submit_password_recovery') ?>" />
		</td>
	</tr>
</table>
</form>