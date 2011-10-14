<?php

if (!defined('BASEPATH'))
	exit('No direct script access allowed');

/**
 * OpenID Library
 *
 * @package    CodeIgniter
 * @author     bardelot, Călin-Andrei Burloiu
 * @see        http://cakebaker.42dh.com/2007/01/11/cakephp-and-openid/
 *             & http://openidenabled.com/php-openid/
 */
class Openid {

	var $storePath = 'tmp';
	var $sreg_enable = FALSE;
	var $sreg_required = NULL;
	var $sreg_optional = NULL;
	var $sreg_policy = NULL;
	var $pape_enable = FALSE;
	var $pape_policy_uris = NULL;
	var $ext_args = NULL;
	var $request_to;
	var $trust_root;

	function __construct()
	{
		$CI = & get_instance();
		$CI->config->load('openid');
		$this->storePath = $CI->config->item('openid_storepath');

		session_start();
		$this->_do_includes();

		log_message('debug', "OpenID Class Initialized");
	}

	function _do_includes()
	{
		set_include_path(dirname(__FILE__) . PATH_SEPARATOR . get_include_path());

		require_once "Auth/OpenID/Consumer.php";
		require_once "Auth/OpenID/FileStore.php";
		require_once "Auth/OpenID/SReg.php";
		require_once "Auth/OpenID/AX.php";
		require_once "Auth/OpenID/PAPE.php";
	}

	function set_sreg($enable, $required = NULL, $optional = NULL, $policy = NULL)
	{
		$this->sreg_enable = $enable;
		$this->sreg_required = $required;
		$this->sreg_optional = $optional;
		$this->sreg_policy = $policy;
	}

	function set_pape($enable, $policy_uris = NULL)
	{
		$this->pape_enable = $enable;
		$this->pape_policy_uris = $policy_uris;
	}

	function set_request_to($uri)
	{
		$this->request_to = $uri;
	}

	function set_trust_root($trust_root)
	{
		$this->trust_root = $trust_root;
	}

	function set_args($args)
	{
		$this->ext_args = $args;
	}

	function _set_message($error, $msg, $val = '', $sub = '%s')
	{
		$CI = & get_instance();
		$CI->lang->load('openid', 'english');
		echo str_replace($sub, $val, $CI->lang->line($msg));

		if ($error)
		{
			exit;
		}
	}

	function authenticate($openId)
	{
		$consumer = $this->_get_consumer();
		$authRequest = $consumer->begin($openId);

		// No auth request means we can't begin OpenID.
		if (!$authRequest)
		{
			$this->_set_message(TRUE, 'openid_auth_error');
		}
		
		if ($this->sreg_enable)
		{
			$sreg_request = Auth_OpenID_SRegRequest::build(
					$this->sreg_required, $this->sreg_optional, 
					$this->sreg_policy);

			if ($sreg_request)
			{
				$authRequest->addExtension($sreg_request);
			}
			else
			{
				$this->_set_message(TRUE, 'openid_sreg_failed');
			}
		}
		
		
		
		// *** TODO ***
		
		// Create attribute request object
		// See http://code.google.com/apis/accounts/docs/OpenID.html#Parameters for parameters
		// Usage: make($type_uri, $count=1, $required=false, $alias=null)
		$attribute[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/contact/email', 1, TRUE);
		$attribute[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/namePerson/first', 1, TRUE);
		$attribute[] = Auth_OpenID_AX_AttrInfo::make(
				'http://axschema.org/namePerson/last', 1, TRUE);

		// Create AX fetch request
		$ax = new Auth_OpenID_AX_FetchRequest;

		// Add attributes to AX fetch request
		foreach($attribute as $attr){
			$ax->add($attr);
		}

		// Add AX fetch request to authentication request
		$authRequest->addExtension($ax);
		
		
		
		if ($this->pape_enable)
		{
			$pape_request = new Auth_OpenID_PAPE_Request($this->pape_policy_uris);

			if ($pape_request)
			{
				$authRequest->addExtension($pape_request);
			}
			else
			{
				$this->_set_message(TRUE, 'openid_pape_failed');
			}
		}

		if ($this->ext_args != NULL)
		{
			foreach ($this->ext_args as $extensionArgument)
			{
				if (count($extensionArgument) == 3)
				{
					$authRequest->addExtensionArg($extensionArgument[0],
							$extensionArgument[1],
							$extensionArgument[2]);
				}
			}
		}

		// Redirect the user to the OpenID server for authentication.
		// Store the token for this authentication so we can verify the
		// response.
		// For OpenID 1, send a redirect.  For OpenID 2, use a Javascript
		// form to send a POST request to the server.
		if ($authRequest->shouldSendRedirect())
		{
			$redirect_url = $authRequest->redirectURL($this->trust_root,
					$this->request_to);

			// If the redirect URL can't be built, display an error
			// message.
			if (Auth_OpenID::isFailure($redirect_url))
			{
				$this->_set_message(TRUE, 'openid_redirect_failed', $redirect_url->message);
			}
			else
			{
				// Send redirect.
				header("Location: " . $redirect_url);
			}
		}
		else
		{
			// Generate form markup and render it.
			$form_id = 'openid_message';
			$form_html = $authRequest->htmlMarkup($this->trust_root,
					$this->request_to, FALSE, array('id' => $form_id));

			// Display an error if the form markup couldn't be generated;
			// otherwise, render the HTML.
			if (Auth_OpenID::isFailure($form_html))
			{
				$this->_set_message(TRUE, 'openid_redirect_failed', $form_html->message);
			}
			else
			{
				print $form_html;
			}
		}
	}

	function get_response()
	{
		$consumer = $this->_get_consumer();
		$response = $consumer->complete($this->request_to);

		return $response;
	}

	function _get_consumer()
	{
		if (!file_exists($this->storePath) && !mkdir($this->storePath))
		{
			$this->_set_message(TRUE, 'openid_storepath_failed', $this->storePath);
		}

		$store = new Auth_OpenID_FileStore($this->storePath);
		$consumer = new Auth_OpenID_Consumer($store);

		return $consumer;
	}

}
