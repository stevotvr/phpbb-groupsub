<?php
/**
 *
 * Group Subscription. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2019, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

/**
* DO NOT CHANGE
*/
if (!defined('IN_PHPBB'))
{
	exit;
}

if (empty($lang) || !is_array($lang))
{
	$lang = array();
}

// DEVELOPERS PLEASE NOTE
//
// All language files should use UTF-8 as their encoding and the files must not contain a BOM.
//
// Placeholders can now contain order information, e.g. instead of
// 'Page %s of %s' you can (and should) write 'Page %1$s of %2$s', this allows
// translators to re-order the output of data while ensuring it remains correct
//
// You do not need this where single placeholders are used, e.g. 'Message %d' is fine
// equally where a string contains only two placeholders which are used to wrap text
// in a url you again do not need to specify an order e.g., 'Click %sHERE%s' is fine
//
// Some characters you may want to copy&paste:
// ’ » “ ” …
//

$lang = array_merge($lang, array(
	'ACP_GROUPSUB_MANAGE_SUBS_EXPLAIN_READONLY'	=> 'Here you can view subscriptions.',
	'ACP_GROUPSUB_MANAGE_SUBS_EXPLAIN'			=> 'Here you can view, modify, and cancel subscriptions.',
	'ACP_GROUPSUB_NO_SUBS'						=> 'No subscriptions',
	'ACP_GROUPSUB_SUB_ADD'						=> 'Create subscription',
	'ACP_GROUPSUB_SUB_ADD_SUCCESS'				=> 'Subscription created successfully',
	'ACP_GROUPSUB_SUB_EDIT'						=> 'Edit subscription',
	'ACP_GROUPSUB_SUB_EDIT_SUCCESS'				=> 'Subscription details saved successfully',
	'ACP_GROUPSUB_SUB_DELETE'					=> 'Cancel',
	'ACP_GROUPSUB_SUB_DELETE_CONFIRM'			=> 'Are you sure you wish to cancel this subscription?',
	'ACP_GROUPSUB_SUB_DELETE_SUCCESS'			=> 'Subscription cancelled successfully',
	'ACP_GROUPSUB_SUB_RESTART'					=> 'Restart',
	'ACP_GROUPSUB_SUB_RESTART_CONFIRM'			=> 'Are you sure you wish to restart this subscription?',
	'ACP_GROUPSUB_SUB_RESTART_SUCCESS'			=> 'Subscription restarted successfully',
	'ACP_GROUPSUB_SUB_DETAILS'					=> 'Subscription details',
	'ACP_GROUPSUB_SUB_USER'						=> 'Subscriber',
	'ACP_GROUPSUB_SUB_PACKAGE'					=> 'Package',
	'ACP_GROUPSUB_SUB_SELECT_PACKAGE'			=> 'Select package',
	'ACP_GROUPSUB_SUB_EXPIRE'					=> 'Expires',
	'ACP_GROUPSUB_SUB_EXPIRE_EXPLAIN'			=> 'Enter the date at which this subscription should end. Leave this field blank for a never-ending subscription.',
	'ACP_GROUPSUB_SUB_START'					=> 'Started',
	'ACP_GROUPSUB_SUB_START_EXPLAIN'			=> 'Enter a starting date for this subscription.',

	'ACP_GROUPSUB_START'	=> 'Started',
	'ACP_GROUPSUB_EXPIRES'	=> 'Expires',
	'ACP_GROUPSUB_STATUS'	=> 'Status',

	'ACP_GROUPSUB_EXPIRES_NEVER'	=> 'Never',
	'ACP_GROUPSUB_ACTIVE'			=> 'Active',
	'ACP_GROUPSUB_ENDED'			=> 'Ended',
	'ACP_GROUPSUB_ALL_PACKAGES'		=> 'All subscription packages',
	'ACP_GROUPSUB_DELETED'			=> 'deleted',

	'ACP_GROUPSUB_ERROR_NO_PKGS'		=> 'There are no packages for which to create a subscription.',
	'ACP_GROUPSUB_ERROR_DATE_IN_PAST'	=> 'The expiration date entered was in the past.',
	'ACP_GROUPSUB_ERROR_INVALID_DATE'	=> 'The date entered was in an invalid format.',
	'ACP_GROUPSUB_ERROR_SUB_CONFLICT'	=> 'This member already has an active subscription to this package.<br><br><a href="%s">Edit active subscription</a>',
));
