<?php echo form_open("user/activate/$user_id/code") ?>
<fieldset>
<legend><?php echo $this->lang->line('user_legend_activation') ?></legend>

<p><?php echo $this->lang->line('user_instruction_activation') ?></p>

<input type="hidden" name="user-id" value="<?php echo $user_id ?>" />

<label for="activation-code" class="strong"><?php echo $this->lang->line('user_activation_code') ?>:</label>
<input type="text" name="activation-code" id="activation-code" value="<?php echo set_value('activation-code') ?>" size="24" />

<input type="submit" value="<?php echo $this->lang->line('user_submit_activate') ?>" />

<p><?php echo form_error('activation-code') ?></p>
</fieldset>
</form>

<?php echo form_open("user/activate/$user_id/resend") ?>
<fieldset>
<legend><?php echo $this->lang->line('user_legend_resend_activation') ?></legend>

<p><?php echo $this->lang->line('user_instruction_resend_activation') ?></p>

<input type="hidden" name="user-id" value="<?php echo $user_id ?>" />

<label for="email" class="strong"><?php echo $this->lang->line('user_email') ?>:</label>
<input type="text" name="email" id="email" value="<?php echo set_value('email', $email) ?>" size="24" />

<input type="submit" value="<?php echo $this->lang->line('user_submit_resend_activation') ?>" />

<p><?php echo form_error('email') ?></p>
</fieldset>
</form>