<?php

// Merge with standard language entries.
include('system/language/english/form_validation_lang.php');

// Login / Register/ Account / Profile
$lang['_is_username_unique']		= 'Username already exists. Please choose another one.';
$lang['_valid_username']			= 'You must enter a valid username with letters, numbers, . (dots) or _ (underscores).';
$lang['_valid_username_or_email']	= "You must enter an e-mail address or a valid username.";
$lang['_do_login']					= 'Wrong %s, or wrong %s.';
$lang['_valid_date']				= 'Invalid %s! Use the specified format or leave the field blank if you don\'t want to specify it.';
$lang['_valid_old_password']		= 'Wrong %s.';
$lang['_change_password_cond']		= 'If you want to change your password complete all three password related fields.';
$lang['_required_by_register']		= 'The %s field is required.';
$lang['_check_captcha']				= 'The text entered does not match the text from the previous image. Try again with this image.';

// Account Activation
$lang['_valid_activation_code']		= 'Invalid activation code. You must provide 16 hexa characters.';
$lang['_do_activate']				= 'Wrong activation code.';
$lang['_do_resend_activation']		= 'An error occurred while resending your activation e-mail. This is not a permanent error. Please try again later.';

// Password Recovery
$lang['_username_exists']			= 'There is no account registered with this username.';
$lang['_internal_account']			= 'You cannot change the password for this account because authentication is provided by a third-party.';
$lang['_do_recover_password']		= 'Username and e-mail address are not associated with the same account.';

// Comment Video
$lang['_is_user_loggedin'] 		= 'In order to comment a video you must be logged in.';

/* End of file form_validation_lang.php */
/* Location: ./system/language/english/form_validation_lang.php */