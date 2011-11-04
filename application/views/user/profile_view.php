<?php 
	if (! isset($tab))
		$tab = 0;
?>

<h1>
	<?php echo $this->lang->line('user_appelation')
	. ' <em>'. $userdata['username']. '</em>' ?>
</h1>

<div id="profile-tabs">
	<ul>
		<li><a href="#tab-profile">Profile</a></li>
		<li><a href="#tab-videos">Videos</a></li>
	</ul>
	<div id="tab-profile">
		<table class="form">
			<tr>
				<td>
				  <?php if ($userdata['picture']): ?>
					<a href="<?php echo $userdata['picture'] ?>"><img src="<?php echo $userdata['picture_thumb'] ?>" style="float: left" /></a>
				  <?php endif ?>
				</td>
				<td></td>
			</tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_username'). ': ' ?></th>
				<td><?php echo $userdata['username'] ?></td>
			</tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_roles'). ': ' ?></th>
				<td><?php echo $userdata['roles'] ?></td>
			</tr>
			
			<tr><td></td><td>&nbsp;</td></tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_first_name'). ': ' ?></th>
				<td><?php echo $userdata['first_name'] ?></td>
			</tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_last_name'). ': ' ?></th>
				<td><?php echo $userdata['last_name'] ?></td>
			</tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_sex'). ': ' ?></th>
				<td><?php
					echo ($userdata['sex']
							? $this->lang->line('user_sex_female')
							: $this->lang->line('user_sex_male') ) ?></td>
			</tr>

			<tr>
				<th><?php echo $this->lang->line('user_birth_date'). ': ' ?></th>
				<td><?php echo $userdata['birth_date'] ?></td>
			</tr>

			<tr>
				<th><?php echo $this->lang->line('user_country'). ': ' ?></th>
				<td><?php echo $userdata['country_name'] ?></td>
			</tr>

			<tr>
				<th><?php echo $this->lang->line('user_locality'). ' : ' ?></th>
				<td><?php echo $userdata['locality'] ?></td>
			</tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_time_zone'). ' : ' ?></th>
				<td><?php echo $userdata['time_zone'] ?></td>
			</tr>
			
			<tr><td></td><td>&nbsp;</td></tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_registration_date'). ' : ' ?></th>
				<td><?php echo $userdata['registration_date'] ?></td>
			</tr>
			
			<tr>
				<th><?php echo $this->lang->line('user_last_login'). ' : ' ?></th>
				<td><?php echo $userdata['last_login'] ?></td>
			</tr>
		</table>
	</div>
	<div id="tab-videos">
		<?php echo $videos_summary ?>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		$('#profile-tabs').tabs({
			"selected": <?php echo $tab ?>
		});
	});
	
</script>