<?php
/**
 * This file implements the UI controller for Groups/Users management.
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2003-2004 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package admin
 */

/**
 * Includes:
 */
require_once( dirname(__FILE__). '/_header.php' );
$AdminUI->setPath( 'users' );
$admin_pagetitle = T_('User management');

param( 'action', 'string', '' );
param( 'user', 'integer', 0 );
param( 'group', 'integer', 0 );


// show the top menu
if( $action != 'userupdate' )
{ // perhaps we'll have to set a cookie later
	require( dirname(__FILE__).'/_menutop.php' );
}


$user_profile_only = 0;
// Check permission:
if( !$current_User->check_perm( 'users', 'edit', false ) )
{
	// allow profile editing/viewing only
	$user_profile_only = 1;

	if( $action && $action != 'userupdate' )
	{ // This should be prevented un the UI
		$Messages->add( 'You have no permission to edit other users or groups!' );
		$action = ''; // don't show group form (we have no group ID)
	}
	elseif( $demo_mode && $action && $current_User->login == 'demouser' )
	{
		$Messages->add( T_('You cannot change the demouser profile in demo mode!') );
	}
}


if( $Messages->count() )
{
	if( $action == 'userupdate' )
	{ // display top menu that was suppressed before
		require( dirname(__FILE__).'/_menutop.php' );
	}
}
else switch ($action)
{ // actions only when editing users is allowed

	case 'newuser':
		param( 'template', 'integer', -1 );

		if( $template > -1 )
		{ // we use a template
			$edited_User = $UserCache->get_by_ID($template);	// Copy !
			$edited_User->set('ID', 0);
		}
		else
		{ // we use an empty user
			$edited_User = & new User();
		}

		break;


	case 'userupdate':
		// Update existing user OR create new user:
		param( 'edited_user_ID', 'integer', true );
		if( $edited_user_ID == 0 )
		{ // we create a new user
			$edited_User = & new User();
			$edited_User->set_datecreated( $localtimenow );
		}
		else
		{ // we edit an existing user:
			$edited_User = & $UserCache->get_by_ID( $edited_user_ID );
		}

		if( $user_profile_only && $edited_user_ID != $current_User->ID )
		{ // user is only allowed to update him/herself
			$Messages->add( T_('You are only allowed to update your own profile!') );

			// display menu
			require( dirname(__FILE__).'/_menutop.php' );
			break;
		}

		param( 'edited_user_oldlogin', 'string', true );
		param( 'edited_user_login', 'string', true );
		$edited_user_login = strtolower( $edited_user_login );

		if( empty($edited_user_login) )
		{
			$Messages->add( T_('You must provide an unique login!') );
		}

		if( !$user_profile_only )
		{ // allow changing level/group not for profile mode
			param( 'edited_user_level', 'integer', true );
			if( $edited_user_level < 0 || $edited_user_level > 10 )
			{
				$Messages->add( sprintf( T_('User level must be between %d and %d.'), 0, 10 ) );
			}
			else
			{
				$edited_User->set( 'level', $edited_user_level );
			}

			param( 'edited_user_grp_ID', 'integer', true );
			if( $edited_user_grp_ID > 0 )
			{
				$edited_user_Group = $GroupCache->get_by_ID( $edited_user_grp_ID );
			}
			else $edited_user_Group = & new Group();

			$edited_User->setGroup( $edited_user_Group );
			// echo 'new group = ';
			// $edited_User->Group->disp('name');
		}

		// check if new login already exists for another user_ID
		$query = "SELECT ID
								FROM T_users
							 WHERE user_login = '$edited_user_login'
							   AND ID != $edited_user_ID";

		if( $q = $DB->get_var( $query ) )
		{
			$Messages->add( sprintf( T_('This login already exists. Do you want to <a %s>edit the existing user</a>?'), 'href="?user='.$q.'"' ));
		}

		param( 'edited_user_firstname', 'string', true );
		param( 'edited_user_lastname', 'string', true );
		param( 'edited_user_nickname', 'string', true );
		param( 'edited_user_idmode', 'string', true );
		param( 'edited_user_locale', 'string', true );
		param( 'edited_user_email', 'string', true );
		param( 'edited_user_url', 'string', true );
		param( 'edited_user_icq', 'string', true );
		param( 'edited_user_aim', 'string', true );
		param( 'edited_user_msn', 'string', true );
		param( 'edited_user_yim', 'string', true );
		param( 'edited_user_notify', 'integer', 0 );
		param( 'edited_user_showonline', 'integer', 0 );
		param( 'edited_user_pass1', 'string', true );
		param( 'edited_user_pass2', 'string', true );

		// Perfom check on parameters:
		profile_check_params( array( 'nickname' => $edited_user_nickname,
																	'icq' => $edited_user_icq,
																	'email' => $edited_user_email,
																	'url' => $edited_user_url,
																	'pass1' => $edited_user_pass1,
																	'pass2' => $edited_user_pass2,
																	'pass_required' => ($edited_user_ID == 0) ) );

		$edited_User->set( 'login', $edited_user_login );
		$edited_User->set( 'firstname', $edited_user_firstname );
		$edited_User->set( 'lastname', $edited_user_lastname );
		$edited_User->set( 'nickname', $edited_user_nickname );
		$edited_User->set( 'idmode', $edited_user_idmode );
		$edited_User->set( 'locale', $edited_user_locale );
		$edited_User->set( 'email', $edited_user_email );
		$edited_User->set( 'url', $edited_user_url );
		$edited_User->set( 'icq', $edited_user_icq );
		$edited_User->set( 'aim', $edited_user_aim );
		$edited_User->set( 'msn', $edited_user_msn );
		$edited_User->set( 'yim', $edited_user_yim );
		$edited_User->set( 'notify', $edited_user_notify );
		$edited_User->set( 'showonline', $edited_user_showonline );

		if( !$Messages->count( 'error' ) )
		{ // OK, no error.

			if( !empty($edited_user_pass2) )
			{ // Password provided, we must encode it
				$new_pass = md5( $edited_user_pass2 );
				$edited_User->set( 'pass', $new_pass ); // set password
			}

			if( $edited_User->get('ID') != 0 )
			{ // Commit update to the DB:
				$edited_User->dbupdate();

				$Messages->add( T_('User updated.'), 'note' );

			}
			else
			{ // Insert user into DB
				$edited_User->dbinsert();
				$Messages->add( T_('New user created.'), 'note' );
			}

			// Update cookies
			if( $edited_user_ID == $current_User->ID )
			{ // current user updates him/herself - we have to set cookies to keep him logged in
				if( isset($new_pass) && $current_User->pass != $new_pass )
				{
					setcookie( $cookie_pass, $new_pass, $cookie_expires, $cookie_path, $cookie_domain);
				}

				if( $current_User->login != $edited_User->login )
				{
					setcookie( $cookie_user, $edited_User->login, $cookie_expires, $cookie_path, $cookie_domain );
				}
			}
		}

		// display menu
		require( dirname(__FILE__).'/_menutop.php' );

		break;


	case 'promote':
		param( 'prom', 'string', true );
		param( 'id', 'integer', true );

		$edited_user_ID = $id;

		$UserToPromote =& $UserCache->get_by_ID( $id );
		$usertopromote_level = $UserToPromote->get( 'level' );

		if( ! in_array($prom, array('up', 'down'))
				|| ($prom == 'up' && $usertopromote_level > 9)
				|| ($prom == 'down' && $usertopromote_level < 1)
			)
		{
			$Messages->add( T_('Invalid promotion.') );
		}
		else
		{
			if( $prom == 'up' )
			{
				$sql = "UPDATE T_users SET user_level=user_level+1 WHERE ID = $id";
			}
			elseif( $prom == 'down' )
			{
				$sql = "UPDATE T_users SET user_level=user_level-1 WHERE ID = $id";
			}

			if( $DB->query( $sql ) )
			{
				$Messages->add( T_('User level changed.'), 'note' );
			}
			else
			{
				$Messages->add( sprintf( 'Couldn\'t change %s\'s level.', $UserToPromote->login ) );
			}
		}
		break;


	case 'deleteuser':
		/*
		 * Delete user
		 */
		param( 'id', 'integer', true );
		param( 'confirm', 'integer', 0 );

		if( $id == $current_User->ID )
		{
			$Messages->add( T_('You can\'t delete yourself!') );
		}
		if( $id == 1 )
		{
			$Messages->add( T_('You can\'t delete User #1!') );
		}

		if( $Messages->count() )
		{
			break;
		}

		$deleted_User = & $UserCache->get_by_ID( $id );

		if( !$confirm )
		{?>
		<div class="panelinfo">
			<h3><?php printf( T_('Delete User %s?'), $deleted_User->get( 'firstname' ).' '.$deleted_User->get( 'lastname' ).' ['.$deleted_User->get( 'login' ).']' )?></h3>

			<p><?php echo T_('Warning').': '.T_('deleting an user also deletes all posts made by this user.') ?></p>

			<p><?php echo T_('THIS CANNOT BE UNDONE!') ?></p>

			<p>
				<?php
					$Form = & new Form( 'b2users.php', 'form', 'get' );

					$Form->begin_form( 'inline' );
					$Form->hidden( 'action', 'deleteuser' );
					$Form->hidden( 'id', $deleted_User->ID );
					$Form->hidden( 'confirm', 1 );
					$Form->button( array( 'submit', '', T_('I am sure!'), 'DeleteButton' ) );
					$Form->end_form();

					$Form->begin_form( 'inline' );
					$Form->submit( array( '', T_('CANCEL'), 'search' ) );
					$Form->end_form();

				?>
			</p>

		</div>
	<?php
		}
		else
		{ // confirmed
			// Delete from DB:
			echo '<div class="panelinfo"><h3>'.T_('Deleting User...').'</h3>';
			$deleted_User->dbdelete( true );
			unset($deleted_User);
			echo '</div>';
		}

		break;


	// ---- GROUPS ----------------------------------------

	case 'newgroup':
		param( 'template', 'integer', -1 );

		if( $template > -1 )
		{ // we use a template
			$edited_Group = $GroupCache->get_by_ID( $template );
			$edited_Group->set('ID', 0);
		}
		else
		{ // we use an empty user
			$edited_Group = & new Group();
		}

		break;


	case 'groupupdate':
		param( 'edited_grp_ID', 'integer', true );
		param( 'edited_grp_oldname', 'string', true );
		param( 'edited_grp_name', 'string', true );

		if( empty($edited_grp_name) )
		{
			$Messages->add( T_('You must provide a group name!') );
		}

		// check if the group name already exists for another group
		$query = "SELECT grp_ID FROM T_groups WHERE grp_name = '$edited_grp_name' AND grp_ID != $edited_grp_ID";

		if( $q = $DB->get_var( $query ) )
		{
			$Messages->add( sprintf( T_('This group name already exists! Do you want to <a %s>edit the existing group</a>?'), 'href="?group='.$q.'"' ));
		}

		if( $edited_grp_ID == 0 )
		{
			$edited_Group = & new Group();
		}
		else
		{
			$edited_Group = $GroupCache->get_by_ID( $edited_grp_ID );
		}

		$edited_Group->set( 'name', $edited_grp_name );

		param( 'edited_grp_perm_blogs', 'string', true );
		$edited_Group->set( 'perm_blogs', $edited_grp_perm_blogs );

		param( 'edited_grp_perm_stats', 'string', true );
		$edited_Group->set( 'perm_stats', $edited_grp_perm_stats );

		param( 'edited_grp_perm_spamblacklist', 'string', true );
		$edited_Group->set( 'perm_spamblacklist', $edited_grp_perm_spamblacklist );

		param( 'edited_grp_perm_options', 'string', true );
		$edited_Group->set( 'perm_options', $edited_grp_perm_options );

		param( 'edited_grp_perm_templates', 'integer', 0 );
		$edited_Group->set( 'perm_templates', $edited_grp_perm_templates );

		if( $edited_grp_ID != 1 )
		{ // Groups others than #1 can be prevented from editing users
			param( 'edited_grp_perm_users', 'string', true );
			$edited_Group->set( 'perm_users', $edited_grp_perm_users );
		}

		if( $Messages->count() )
		{
			break;
		}

		if( $edited_grp_ID == 0 )
		{ // Insert into the DB:
			$edited_Group->dbinsert();
		}
		else
		{ // Commit update to the DB:
			$edited_Group->dbupdate();
		}
		// Commit changes in cache:
		$GroupCache->add( $edited_Group );

		// remember to display the forms
		$group = $edited_grp_ID;

		$Messages->add( T_('Group updated.'), 'note' );
		break;


	case 'deletegroup':
		/*
		 * Delete group
		 */
		param( 'id', 'integer', true );
		param( 'confirm', 'integer', 0 );

		if( $id == 1 )
		{
			$Messages->add( T_('You can\'t delete Group #1!') );
		}
		if( $id == $Settings->get('newusers_grp_ID' ) )
		{
			$Messages->add( T_('You can\'t delete the default group for new users!') );
		}

		if( $Messages->count() )
		{
			break;
		}

		$del_Group = $GroupCache->get_by_ID( $id );

		if( !$confirm )
		{?>
		<div class="panelinfo">
			<h3><?php printf( T_('Delete group &laquo;%s&raquo;?'), $del_Group->get( 'name' ) )?></h3>

			<p><?php echo T_('THIS CANNOT BE UNDONE!') ?></p>

			<p>
				<form action="b2users.php" method="get" class="inline">
					<input type="hidden" name="action" value="deletegroup" />
					<input type="hidden" name="id" value="<?php $del_Group->ID() ?>" />
					<input type="hidden" name="confirm" value="1" />

					<input type="submit" value="<?php echo T_('I am sure!') ?>" class="search" />
				</form>
				<form action="b2users.php" method="get" class="inline">
					<input type="submit" value="<?php echo T_('CANCEL') ?>" class="search" />
				</form>
			</p>

		</div>
	<?php
		}
		else
		{ // confirmed
			// Delete from DB:
			$del_Group->dbdelete( true );
			unset($del_Group);
			$Messages->add( T_('Group deleted...'), 'note' );
		}

		break;
}


if( $Messages->count( 'all' ) )
{ // we have errors/notes
	?>
	<div class="panelinfo">
	<?php

	$Messages->display(
		(isset( $edited_user_ID ) ?
			(($edited_user_ID == 0) ? T_('The user was not created:') : T_('The user was not updated:'))
		: (isset( $edited_grp_ID) ?
			(($edited_grp_ID == 0) ? T_('The group was not created:') : T_('The group was not updated:'))
			: '')
		), '', true, 'error');

	$Messages->display( '', '', true, 'note' );
	?>
	</div>
	<?php
}


if( $current_User->check_perm( 'users', 'view', false ) )
{
	// get the userlist
	$request = "SELECT T_users.*, grp_ID, grp_name
							FROM T_users RIGHT JOIN T_groups ON user_grp_ID = grp_ID
							ORDER BY grp_name, user_login";
	$userlist = $DB->get_results( $request );


	if( ($group != 0) || in_array($action, array( 'newgroup', 'groupupdate' ))  )
	{ // display group form
		if( !isset($edited_Group) )
		{
			$edited_Group = $GroupCache->get_by_ID( $group );
		}
		require(dirname(__FILE__). '/_users_groupform.php');
	}
}
else
{ // user is not allowed to view users
	if( $user == 0 )
	{ // display only current user's form
		$user = $current_User->ID;
	}
	elseif( $user != $current_User->ID )
	{ // another user requested -> error-note
		Log::display( '', '', T_('You are not allowed to view other users.'), 'error' );
		$user = $current_User->ID;
	}
}


// user form
if( $user != 0 || in_array($action, array( 'newuser', 'userupdate' )) )
{ // Display user form
	if( !isset($edited_User) )
	{
		$edited_User = & $UserCache->get_by_ID( $user );
	}

	require(dirname(__FILE__). '/_users_form.php');
}


// users list
if( $current_User->check_perm( 'users', 'view', false ) )
{ // Display user list:
	require( dirname(__FILE__). '/_users_list.php' );
}


require( dirname(__FILE__). '/_footer.php' );
?>