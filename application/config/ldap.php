<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

// LDAP configuration parameters.

$config['ldap_server'] = "ldaps://ldap.grid.pub.ro/";
$config['ldap_req_ou'] = array("Calculatoare", "Profesori");
$config['ldap_bind_user'] = "uid=WUSO,ou=Special Users,dc=cs,dc=curs,dc=pub,dc=ro";
$config['ldap_bind_password'] = "BreSath5";