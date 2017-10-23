<?php
/**
 *
 * Group Subscription. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\groupsub\notification\type;

/**
 * Group Subscription expiration notification.
 */
class expired extends base_type
{
	static public $notification_option = array(
		'lang'	=> 'GROUPSUB_NOTIFICATION_TYPE_EXPIRED',
		'group'	=> 'GROUPSUB_NOTIFICATION_GROUP',
	);

	public function get_type()
	{
		return 'stevotvr.groupsub.notification.type.expired';
	}

	public function get_title()
	{
		return $this->language->lang('GROUPSUB_NOTIFICATION_EXPIRED_TITLE');
	}

	public function get_reference()
	{
		return $this->language->lang('GROUPSUB_NOTIFICATION_EXPIRED_REFERENCE', $this->get_data('gs_name'));
	}

	public function get_email_template()
	{
		return '@stevotvr_groupsub/subscription_expired';
	}

	public function get_email_template_variables()
	{
		return array(
			'SUB_NAME'		=> $this->get_data('gs_name'),

			'U_VIEW_SUB'	=> generate_board_url() . $this->get_url(),
		);
	}
}