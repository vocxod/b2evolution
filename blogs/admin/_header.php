<?php
/**
 * This file initializes the admin/backoffice!
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/license.html}
 * @copyright (c)2003-2005 by Francois PLANQUE - {@link http://fplanque.net/}
 *
 * @package admin
 */

/**
 * Do the MAIN initializations:
 */
require_once( dirname(__FILE__).'/../conf/_config.php' );
$login_required = true;
require_once( dirname(__FILE__).'/'.$admin_dirout.$core_subdir.'_main.inc.php' );

param( 'blog', 'integer', 0, true ); // We may need this for the urls
param( 'mode', 'string', '' );  // Sidebar, bookmarklet


/**
 * Load the AdminUI class for the skin.
 */
require_once( dirname(__FILE__).'/'.$adminskins_subdir.$admin_skin.'/_adminUI.class.php' );
/**
 * This is the Admin UI object which handles the UI for the backoffice.
 *
 * @global AdminUI
 */
$AdminUI =& new AdminUI();


// TODO: we might move this to some file and include it, but OTOH the call of a method allows to dismiss entries
//       based on permissions or user options/profile.
$AdminUI->addMenuEntries( NULL, // root
													array(
														'new' => array( 'text'=>T_('Write'),
																						'href' => 'b2edit.php?blog='.$blog,
																						'style' => 'font-weight: bold;' ),

														'edit' => array( 'text'=>T_('Edit'),
																							'href'=>'b2browse.php?blog='.$blog,
																							'style'=>'font-weight: bold;' ),

														'cats' => array( 'text'=>T_('Categories'),
																							'href'=>'b2categories.php?blog='.$blog ),

														'blogs' => array( 'text'=>T_('Blogs'),
																							'href'=>'blogs.php',
																							'entries' => array(
																								'general' => array(
																									'text' => T_('General'),
																									'href' => 'blogs.php?tab=general&amp;action=edit&amp;blog='.$blog ),
																								'perm' => array(
																									'text' => T_('Permissions'),
																									'href' => 'blogs.php?tab=perm&amp;action=edit&amp;blog='.$blog ),
																								'advanced' => array(
																									'text' => T_('Advanced'),
																									'href' => 'blogs.php?tab=advanced&amp;action=edit&amp;blog='.$blog ),
																							)
																						),

														'stats' => array( 'text'=>T_('Stats'),
																							'perm_name'=>'stats',
																							'perm_level'=>'view',
																							'href'=>'b2stats.php',
																							'entries' => array(
																								'summary' => array(
																									'text' => T_('Summary'),
																									'href' => 'b2stats.php?tab=summary&amp;blog='.$blog ),
																								'other' => array(
																									'text' => T_('Direct Accesses'),
																									'href' => 'b2stats.php?tab=other&amp;blog='.$blog ),
																								'referers' => array(
																									'text' => T_('Referers'),
																									'href' => 'b2stats.php?tab=referers&amp;blog='.$blog ),
																								'refsearches' => array(
																									'text' => T_('Refering Searches'),
																									'href' => 'b2stats.php?tab=refsearches&amp;blog='.$blog ),
																								'syndication' => array(
																									'text' => T_('Syndication'),
																									'href' => 'b2stats.php?tab=syndication&amp;blog='.$blog ),
																								'useragents' => array(
																									'text' => T_('User Agents'),
																									'href' => 'b2stats.php?tab=useragents&amp;blog='.$blog ),
																							)
																						),

														'antispam' => array( 'text'=>T_('Antispam'),
																									'perm_name'=>'spamblacklist',
																									'perm_level'=>'view',
																									'href'=>'b2antispam.php' ),

														'templates' => array( 'text'=>T_('Templates'),
																									'perm_name'=>'templates',
																									'perm_level'=>'any',
																									'href'=>'b2template.php' ),

														'users' => array( 'text'=>T_('Users & Groups'),
																							'perm_name'=>'users',
																							'perm_level'=>'view',
																							'text_noperm'=>T_('User Profile'),	// displayed if perm not granted
																							'href'=>'b2users.php' ),

														'files' => array( 'text'=>T_('Files'),
																							'href'=>'files.php',
																							'perm_eval' => 'return $current_User->level == 10;'),

														'options' => array( 'text' => T_('Settings'),
																								'perm_name' => 'options',
																								'perm_level' => 'view',
																								'href' => 'b2options.php',
																								'entries' => array(
																									'general' => array(
																										'text' => T_('General'),
																										'href' => 'b2options.php?tab=general' ),
																									'regional' => array(
																										'text' => T_('Regional'),
																										'href' => 'b2options.php?tab=regional'.( (isset($loc_transinfo) && $loc_transinfo) ? '&amp;loc_transinfo=1' : '' ) ),
																									'files' => array(
																										'text' => T_('Files'),
																										'href' =>'fileset.php' ),
																									'statuses' => array(
																										'text' => T_('Post statuses'),
																										'href' => 'statuses.php'),
																									'types' => array(
																										'text' => T_('Post types'),
																										'href' => 'types.php'),
																									'plugins' => array(
																										'text' => T_('Plug-ins'),
																										'href' => 'plugins.php'),
																								) ),

														'tools' => array( 'text'=>T_('Tools'),
																							'href'=>'tools.php' ),
													),
													'menu_main' // template name
										);



?>