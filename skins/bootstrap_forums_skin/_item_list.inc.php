<?php
/**
 * This is the template that displays the item block in list
 *
 * This file is not meant to be called directly.
 * It is meant to be called by an include in the main.page.php template (or other templates)
 *
 * b2evolution - {@link http://b2evolution.net/}
 * Released under GNU GPL License - {@link http://b2evolution.net/about/gnu-gpl-license}
 * @copyright (c)2003-2016 by Francois Planque - {@link http://fplanque.com/}
 *
 * @package evoskins
 * @subpackage bootstrap_forums
 */
if( !defined('EVO_MAIN_INIT') ) die( 'Please, do not access this page directly.' );

// Default params:
$params = array_merge( array(
		'post_navigation' => 'same_category', // In this skin, it makes no sense to navigate in any different mode than "same category"
	), $params );

global $Item, $cat;

/**
 * @var array Save all statuses that used on this page in order to show them in the footer legend
 */
global $legend_statuses, $legend_icons;

if( ! is_array( $legend_statuses ) )
{ // Init this array only first time
	$legend_statuses = array();
}
if( ! is_array( $legend_icons ) )
{ // Init this array only first time
	$legend_icons = array();
}

// Calculate what comments has the Item:
$comments_number = generic_ctp_number( $Item->ID, 'comments', get_inskin_statuses( $Item->get_blog_ID(), 'comment' ) );

$status_icon = 'fa-comments';
$status_title = '';
$status_alt = T_('Discussion topic');
$legend_icons['topic_default'] = 1;
if( $Item->is_featured() || $Item->is_intro() )
{ // Special icon for featured & intro posts
	$status_icon = 'fa-bullhorn';
	$status_alt = T_('Sticky topic / Announcement');
	$status_title = '<strong>'.T_('Sticky').':</strong> ';
	$legend_icons['topic_sticky'] = 1;
}
elseif( $Item->comment_status == 'closed' || $Item->comment_status == 'disabled' || $Item->is_locked() )
{ // The post is closed for comments
	$status_icon = 'fa-lock';
	$status_alt = T_('This topic is locked: you cannot edit posts or make replies.');
	$legend_icons['topic_locked'] = 1;
}
elseif( $comments_number > 25 )
{ // Popular topic is when coummnets number is more than 25
	$status_icon = 'fa-star';
	$status_alt = T_('Popular topic');
	$legend_icons['topic_popular'] = 1;
}
$Item->load_Blog();
$use_workflow = $Item->Blog->get_setting( 'use_workflow' );
?>

<article class="container group_row posts_panel">
	<!-- Post Block -->
	<div class="ft_status__ft_title col-lg-8 col-md-8 col-sm-6 col-xs-12">

		<!-- Thread icon -->
		<div class="ft_status_topic">
			<a href="<?php echo $Item->permanent_url(); ?>">
				<i class="icon fa <?php echo $status_icon; ?>" title="<?php echo $status_alt; ?>"
				<?php
				if( $use_workflow )
				{ // ==========================================================================================================================
					$priority_color = item_priority_color( $Item->priority );
					echo ' style="color: '.$priority_color.'; border-color: '.$priority_color.';"';
				} // ==========================================================================================================================
				?>
				></i>
			</a>
		</div>

		<!-- Title / excerpt -->
		<div class="ft_title">
			<div class="posts_panel_title_wrapper">
				<div class="cell1">
					<div class="wrap">
						<?php
						echo $status_title;

						if( $Item->Blog->get_setting( 'track_unread_content' ) )
						{ // Display icon about unread status
							$Item->display_unread_status();
							// Update legend array to display the unread status icons in footer legend:
							switch( $Item->get_read_status() )
							{
								case 'new':
									$legend_icons['topic_new'] = 1;
									break;
								case 'updated':
									$legend_icons['topic_updated'] = 1;
									break;
							}
						}

						// Title:
						$Item->title( array(
								'link_class'      => 'topictitle ellipsis'.( $Item->get_read_status() != 'read' ? ' unread' : '' ),
								'post_navigation' => $params['post_navigation'],
							) );
						?>
					</div>
				</div>

				<?php
				if( $Skin->enabled_status_banner( $Item->status ) )
				{ // Status:
					$Item->format_status( array(
							'template' => '<div class="cell2"><div class="evo_status evo_status__$status$ badge">$status_title$</div></div>',
						) );
					$legend_statuses[] = $Item->status;
				}
				?>

			</div>
			<?php
			$Item->excerpt( array(
					'before' => '<div class="small ellipsis">',
					'after'  => '</div>',
				) );
			?>
		</div>

		<!-- Chapter -->
		<div class="ft_author_info ellipsis">
			<?php echo sprintf( T_('In %s'), $Item->get_chapter_links() ); ?>
		</div>

		<!-- Author -->
		<div class="ft_author_info ellipsis">
			<?php
			// Author info: (THIS HAS DOFFERENT RWD MOVES FROM WHAT'S ABOVE, so it should be in a different div)
			echo T_('Started by');
			$Item->author( array( 'link_text' => 'auto', 'after' => '' ) );
			echo ', '.mysql2date( 'D M j, Y H:i', $Item->datecreated );
			?>
		</div>

		<!-- Author (shrinked) -->
		<div class="ft_author_info shrinked ellipsis">
			<?php
			// Super small screen size Author info:
			echo T_('By');
			$Item->author( array( 'link_text' => 'auto', 'after' => '' ) );
			echo ', '.mysql2date( 'M j, Y', $Item->datecreated );
			?>
		</div>
	</div>

	<!-- Replies Block -->
	<?php
	if( ! $use_workflow )
	{ // --------------------------------------------------------------------------------------------------------------------------
		echo '<div class="ft_count col-lg-1 col-md-1 col-sm-1 col-xs-5">';
		if( $comments_number == 0 && $Item->comment_status == 'disabled' )
		{ // The comments are disabled:
			echo T_('n.a.');
		}
		else if( $latest_Comment = & $Item->get_latest_Comment() )
		{	// At least one reply exists:
			printf( T_('%s replies'), '<div><a href="'.$latest_Comment->get_permanent_url().'" title="'.T_('View latest comment').'">'.$comments_number.'</a></div>' );
		}
		else
		{	// No replies yet:
			printf( T_('%s replies'), '<div>0</div>' );
		}

		echo '</div>';
	} // --------------------------------------------------------------------------------------------------------------------------

	echo '<!-- Assigned User Block -->';
	if( $use_workflow )
	{ // ==========================================================================================================================
		$assigned_User = $Item->get_assigned_User();

		echo '<div class="ft_assigned col-lg-2 col-md-2 col-sm-3 col-xs-4 col-sm-offset-0 col-xs-offset-2">';
		if( $assigned_User )
		{
			echo '<span>'.T_('Assigned to:').'</span>';
			echo '<br />';

			// Assigned user avatar
			$Item->assigned_to2( array(
					'thumb_class' => 'ft_assigned_avatar',
					'link_class' => 'ft_assigned_avatar',
					'thumb_size'   => 'crop-top-32x32'
				) );

			// Assigned user login
			$Item->assigned_to2( array(
					'link_text' => 'name'
				) );
			echo '<br />';
		}
		else
		{
			echo '<span>'.T_('Not assigned').'</span>';
			echo '<br />';
		}

		// Workflow status
		echo '<span style="white-space: nowrap;">'.item_td_task_cell( 'status', $Item, false ).'</span>';
		echo '</div>';
	}	// ==========================================================================================================================

	echo '<!-- Last Comment Block -->';
	if( $use_workflow )
	{ // ==========================================================================================================================
		echo '<div class="ft_date col-lg-2 col-md-2 col-sm-3">';

		if( $comments_number == 0 && $Item->comment_status == 'disabled' )
		{ // The comments are disabled:
			echo T_('n.a.');
		}
		else if( $latest_Comment = & $Item->get_latest_Comment() )
		{	// At least one reply exists:
			printf( T_('%s replies'), '<a href="'.$latest_Comment->get_permanent_url().'" title="'.T_('View latest comment').'">'.$comments_number.'</a>' );
		}
		else
		{	// No replies yet:
			printf( T_('%s replies'), '0' );
		}
		echo '<br />';
	} // ==========================================================================================================================
	else
	{ // --------------------------------------------------------------------------------------------------------------------------
		echo '<div class="ft_date col-lg-3 col-md-3 col-sm-4">';
	} // --------------------------------------------------------------------------------------------------------------------------

	if( $latest_Comment = & $Item->get_latest_Comment() )
	{ // Display info about last comment
		$latest_Comment->author2( array(
					'before'      => '',
					'after'       => '',
					'before_user' => '',
					'after_user'  => '',
					'link_text'   => 'only_avatar',
					'link_class'  => 'ft_author_avatar',
					'thumb_class' => 'ft_author_avatar',
				) );

		// Last comment date
		$latest_Comment->date( $use_workflow ? 'm/d/y' : 'D M j, Y H:i' );

		// Last comment author
		$latest_Comment->author2( array(
				'before'      => '<br />',
				'before_user' => '<br />',
				'after'       => '',
				'after_user'  => '',
				'link_text'   => 'auto'
			) );

		echo ' <a href="'.$latest_Comment->get_permanent_url().'" title="'.T_('View latest post')
				.'" class="icon_latest_reply"><i class="fa fa-arrow-right"></i>&nbsp;<i class="fa fa-file-o"></i></a>';
	}
	else
	{ // No comments, Display info of post
		$Item->author( array(
					'before'      => '',
					'after'       => '',
					'before_user' => '',
					'after_user'  => '',
					'link_text'   => 'only_avatar',
					'link_class'  => 'ft_author_avatar'
				) );
		echo $use_workflow ? $Item->get_mod_date( 'm/d/y' ) : $Item->get_mod_date( 'D M j, Y H:i' );
		echo $Item->author( array(
				'before'    => '<br />',
				'link_text' => 'auto',
			) );
		echo ' <a href="'.$Item->get_permanent_url().'" title="'.T_('View latest post')
				.'" class="icon_latest_reply"><i class="fa fa-arrow-right"></i>&nbsp;<i class="fa fa-file-o"></i></a>';
	}
	echo '</div>';
	?>

	<!-- This is shrinked date that applies on lower screen res -->
	<div class="ft_date_shrinked item_list<?php echo $use_workflow ? ' col-xs-5' : ' col-xs-6'; ?>">
		<?php
		if( $latest_Comment = & $Item->get_latest_Comment() )
		{ // Display info about last comment
			$latest_Comment->date('m/j/y ');
			$latest_Comment->author2( array(
					'link_text'   => 'login'
				) );

			echo ' <a href="'.$latest_Comment->get_permanent_url().'" title="'.T_('View latest post')
					.'" class="icon_latest_reply"><i class="fa fa-arrow-right"></i>&nbsp;<i class="fa fa-file-o"></i></a>';
		}
		else
		{ // No comments, Display info of post
			echo $Item->get_mod_date( 'm/j/y' );
			echo $Item->author( array(
					'link_text' => 'auto',
				) );
			echo ' <a href="'.$Item->get_permanent_url().'" title="'.T_('View latest post').
					'" class="icon_latest_reply"><i class="fa fa-arrow-right"></i>&nbsp;<i class="fa fa-file-o"></i></a>';
		}
		?>
	</div>
</article>