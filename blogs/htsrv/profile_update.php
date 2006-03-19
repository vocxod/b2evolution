<?php
/**
 * This file updates the current user's profile!
 *
 * This file is part of the b2evolution/evocms project - {@link http://b2evolution.net/}.
 * See also {@link http://sourceforge.net/projects/evocms/}.
 *
 * @copyright (c)2003-2005 by Francois PLANQUE - {@link http://fplanque.net/}.
 * Parts of this file are copyright (c)2004-2005 by Daniel HAHLER - {@link http://thequod.de/contact}.
 *
 * @license http://b2evolution.net/about/license.html GNU General Public License (GPL)
 *
 * {@internal Open Source relicensing agreement:
 * Daniel HAHLER grants Francois PLANQUE the right to license
 * Daniel HAHLER's contributions to this file and the b2evolution project
 * under any OSI approved OSS license (http://www.opensource.org/licenses/).
 * }}
 *
 * @package htsrv
 *
 * {@internal Below is a list of authors who have contributed to design/coding of this file: }}
 * @author fplanque: Francois PLANQUE
 * @author blueyed: Daniel HAHLER
 *
 *
 * @todo integrate it into the skins to avoid ugly die() on error and confusing redirect on success.
 *
 * @version $Id$
 */

/**
 * Initialize everything:
 */
require_once dirname(__FILE__).'/../conf/_config.php';

require_once $inc_path.'_main.inc.php';

// Getting GET or POST parameters:
param( 'checkuser_id', 'integer', '' );
param( 'newuser_firstname', 'string', '' );
param( 'newuser_lastname', 'string', '' );
param( 'newuser_nickname', 'string', '' );
param( 'newuser_idmode', 'string', '' );
param( 'newuser_locale', 'string', $default_locale );
param( 'newuser_icq', 'string', '' );
param( 'newuser_aim', 'string', '' );
param( 'newuser_msn', 'string', '' );
param( 'newuser_yim', 'string', '' );
param( 'newuser_url', 'string', '' );
param( 'newuser_email', 'string', '' );
param( 'newuser_allow_msgform', 'integer', 0 ); // checkbox
param( 'newuser_notify', 'integer', 0 );        // checkbox
param( 'newuser_showonline', 'integer', 0 );    // checkbox
param( 'pass1', 'string', '' );
param( 'pass2', 'string', '' );

/**
 * Basic security checks:
 */
if( ! is_logged_in() )
{ // must be logged in!
	die( T_('You are not logged in.') );
}

if( $checkuser_id != $current_User->ID )
{ // Can only edit your own profile
	die( 'You are not logged in under the same account you are trying to modify.' );
}

if( $demo_mode && ($current_User->login == 'demouser') )
{
	die( 'Demo mode: you can\'t edit the demouser profile!<br />[<a href="javascript:history.go(-1)">'
		. T_('Back to profile') . '</a>]' );
}

/**
 * Additional checks:
 */
profile_check_params( array(
	'nickname' => $newuser_nickname,
	'icq' => $newuser_icq,
	'email' => $newuser_email,
	'url' => $newuser_url,
	'pass1' => $pass1,
	'pass2' => $pass2,
	'pass_required' => false ), $current_User );


if( $Messages->count( 'error' ) )
{
	$Messages->display( T_('Cannot update profile. Please correct the following errors:'),
		'[<a href="javascript:history.go(-1)">' . T_('Back to profile') . '</a>]' );
	die();
}


// Do the update:

$updatepassword = '';
if( !empty($pass1) )
{
	$newuser_pass = md5($pass1);
	$current_User->set( 'pass', $newuser_pass );
}

$current_User->set( 'firstname', $newuser_firstname );
$current_User->set( 'lastname', $newuser_lastname );
$current_User->set( 'nickname', $newuser_nickname );
$current_User->set( 'icq', $newuser_icq );
$current_User->set( 'email', $newuser_email );
$current_User->set( 'url', $newuser_url );
$current_User->set( 'aim', $newuser_aim );
$current_User->set( 'msn', $newuser_msn );
$current_User->set( 'yim', $newuser_yim );
$current_User->set( 'idmode', $newuser_idmode );
$current_User->set( 'locale', $newuser_locale );
$current_User->set( 'allow_msgform', $newuser_allow_msgform );
$current_User->set( 'notify', $newuser_notify );
$current_User->set( 'showonline', $newuser_showonline );


// Set Messages into user's session, so they get restored on the next page (after redirect):
$action_Log = new Log();
if( $current_User->dbupdate() )
{
	$action_Log->add( T_('Your profile has been updated.'), 'success' );
}
else
{
	$action_Log->add( T_('Your profile has not been changed.'), 'note' );
}

$Session->set( 'Messages', $action_Log );

header_nocache();
header_redirect();
?>