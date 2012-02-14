<?php
	// Categories
	foreach ($this->config->item('categories') as $id => $name)
	{
		$categories[$id] = $this->lang->line("ui_categ_$name");
	}
?>

<?php echo form_open_multipart("video/upload") ?>
<table class="form">
	<tr>
		<td></td>
		<td><span class="required"><?php echo $this->lang->line('ui_required_fields') ?></span></td>
	</tr>
	
	<tr><td></td><td>&nbsp;</td></tr>

	<tr>
		<th><?php echo $this->lang->line('video_upload_file') ?> <span class="required">*</span> : </th>
		<td><input type="file" name="video-upload-file" size="32" /></td>
	</tr>
	<tr><td></td><td><?php echo form_error('video-upload-file') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>

	<tr>
		<th><?php echo $this->lang->line('video_title') ?> <span class="required">*</span> : </th>
		<td><input type="text" name="video-title" value="<?php echo set_value('video-title') ?>" size="64" /></td>
	</tr>
	<tr><td></td><td><?php echo form_error('video-title') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('video_description') ?> <span class="required">*</span> : </th>
		<td><textarea name="video-description" rows="4" cols="32"><?php echo set_value('video-description') ?></textarea></td>
	</tr>
	<tr><td></td><td><?php echo form_error('video-description') ?></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('video_category') ?> <span class="required">*</span> : </th>
		<td><?php echo form_dropdown('video-category', $categories, 
				// TODO set_value not working
				set_value('video-category', '1')) ?></td>
	</tr>
	<tr><td></td><td></td></tr>
	
	<tr>
		<th><?php echo $this->lang->line('video_tags') ?> <span class="required">*</span> : </th>
		<td><input type="text" name="video-tags" value="<?php echo set_value('video-tags') ?>" size="16" /> (<?php echo $this->lang->line('video_tags_hint') ?>)</td>
	</tr>
	<tr><td></td><td><?php echo form_error('video-tags') ?></td></tr>
	
	<tr><td></td><td>&nbsp;</td></tr>
	
	<tr>
		<td></td>
		<td><input type="submit" value="<?php echo $this->lang->line('video_submit_upload') ?>" /></td>
	</tr>
</table>
</form>