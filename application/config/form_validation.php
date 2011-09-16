<?php

$config = array(
	'signin'=> array(
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
			'rules'=>'trim|required|min_length[5]|max_length[32]'
				. '|strtolower|callback__valid_username'
		),
		array(
			'field'=>'password',
			'label'=>'lang:user_password',
			'rules'=>'required'
		),
		array(
			'field'=>'password-confirmation',
			'label'=>'lang:user_password_confirmation',
			'rules'=>'required'
		),
		array(
			'field'=>'email',
			'label'=>'lang:user_email',
			'rules'=>'required'
		),
		array(
			'field'=>'first-name',
			'label'=>'lang:user_first_name',
			'rules'=>'required'
		),
		array(
			'field'=>'last-name',
			'label'=>'lang:user_last_name',
			'rules'=>'required'
		),
		array(
			'field'=>'birth-date',
			'label'=>'lang:user_birth_date',
			'rules'=>''
		),
		array(
			'field'=>'locality',
			'label'=>'lang:user_locality',
			'rules'=>''
		)
	)
);

/* End of file form_validation.php */
/* Location: ./application/config/form_validation.php */