<?php

$config = array(
	'login'=> array(
		array(
			'field'=>'username',
			'label'=>'lang:user_username_or_email',
			'rules'=>'trim|required|min_length[5]|max_length[32]'
				. '|strtolower|callback__valid_username_or_email'
				. '|callback__do_login[password]'
		),
		array(
			'field'=>'password',
			'label'=>'lang:user_password',
			'rules'=>'required|min_length[5]|max_length[32]'
		)
	),
	'register'=> array(
		array(
			'field'=>'username',
			'label'=>'lang:user_username',
			'rules'=>'trim|callback__required_by_register|min_length[5]|max_length[32]'
				. '|strtolower|xss_clean|callback__valid_username'
		),
		array(
			'field'=>'password',
			'label'=>'lang:user_password',
			'rules'=>'callback__required_by_register|min_length[5]|max_length[32]'
		),
		array(
			'field'=>'password-confirmation',
			'label'=>'lang:user_password_confirmation',
			'rules'=>'callback__required_by_register|matches[password]'
		),
		array(
			'field'=>'old-password',
			'label'=>'lang:user_old_password',
			'rules'=>'min_length[5]|max_length[32]|callback__valid_old_password[username]'
		),
		array(
			'field'=>'new-password',
			'label'=>'lang:user_new_password',
			'rules'=>'min_length[5]|max_length[32]'
		),
		array(
			'field'=>'new-password-confirmation',
			'label'=>'lang:user_new_password_confirmation',
			'rules'=>'callback__change_password_cond|matches[new-password]'
		),
		array(
			'field'=>'email',
			'label'=>'lang:user_email',
			'rules'=>'trim|required|xss_clean|valid_email'
		),
		array(
			'field'=>'first-name',
			'label'=>'lang:user_first_name',
			'rules'=>'trim|required|ucwords|xss_clean|prep_for_form'
		),
		array(
			'field'=>'last-name',
			'label'=>'lang:user_last_name',
			'rules'=>'trim|required|ucwords|xss_clean|prep_for_form'
		),
		array(
			'field'=>'birth-date',
			'label'=>'lang:user_birth_date',
			'rules'=>'trim|callback__valid_date'
		),
		array(
			'field'=>'locality',
			'label'=>'lang:user_locality',
			'rules'=>'trim|ucwords|xss_clean|prep_for_form'
		)
	),
	'activate'=> array(
		array(
			'field'=>'activation-code',
			'label'=>'lang:user_activation_code',
			'rules'=>'trim|required|strtolower|callback__valid_activation_code|callback__do_activate'
		)
	),
	'resend_activation'=> array(
		array(
			'field'=>'email',
			'label'=>'lang:user_email',
			'rules'=>'trim|required|xss_clean|valid_email|callback__do_resend_activation'
		)
	),
	'recover_password'=> array(
		array(
			'field'=>'username',
			'label'=>'lang:user_username',
			'rules'=>'trim|required|min_length[5]|max_length[32]'
				. '|strtolower|callback__valid_username|callback__username_exists|callback__internal_account'
				. '|callback__do_recover_password'
		),
		array(
			'field'=>'email',
			'label'=>'lang:user_email',
			'rules'=>'trim|required|xss_clean|valid_email'
		)
	)
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */