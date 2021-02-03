<?php
/**
 *
 * Group Subscription. An extension for the phpBB Forum Software package.
 *
 * @copyright (c) 2017, Steve Guidetti, https://github.com/stevotvr
 * @license GNU General Public License, version 2 (GPL-2.0)
 *
 */

namespace stevotvr\groupsub\controller;

use phpbb\auth\auth;
use phpbb\config\config;
use phpbb\config\db_text;
use phpbb\controller\helper;
use phpbb\exception\http_exception;
use phpbb\language\language;
use phpbb\request\request_interface;
use phpbb\template\template;
use phpbb\user;
use stevotvr\groupsub\operator\currency_interface;
use stevotvr\groupsub\operator\package_interface;
use stevotvr\groupsub\operator\subscription_interface;
use stevotvr\groupsub\operator\unit_helper_interface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Group Subscription controller for the main user-facing interface.
 */
class main_controller
{
	/**
	 * @var auth
	 */
	protected $auth;

	/**
	 * @var config
	 */
	protected $config;

	/**
	 * @var db_text
	 */
	protected $config_text;

	/**
	 * @var currency_interface
	 */
	protected $currency;

	/**
	 * @var helper
	 */
	protected $helper;

	/**
	 * @var language
	 */
	protected $language;

	/**
	 * @var package_interface
	 */
	protected $pkg_operator;

	/**
	 * @var request_interface
	 */
	protected $request;

	/**
	 * @var subscription_interface
	 */
	protected $sub_operator;

	/**
	 * @var template
	 */
	protected $template;

	/**
	 * @var unit_helper_interface
	 */
	protected $unit_helper;

	/**
	 * @var user
	 */
	protected $user;

	/**
	 * The root phpBB path.
	 *
	 * @var string
	 */
	protected $root_path;
	/**
	 * The script file extension.
	 *
	 * @var string
	 */
	protected $php_ext;

	/**
	 * @param auth                   $auth
	 * @param config                 $config
	 * @param db_text                $config_text
	 * @param currency_interface     $currency
	 * @param helper                 $helper
	 * @param language               $language
	 * @param package_interface      $pkg_operator
	 * @param request_interface      $request
	 * @param subscription_interface $sub_operator
	 * @param template               $template
	 * @param unit_helper_interface  $unit_helper
	 * @param user                   $user
	 */
	public function __construct(auth $auth, config $config, db_text $config_text, currency_interface $currency, helper $helper, language $language, package_interface $pkg_operator, request_interface $request, subscription_interface $sub_operator, template $template, unit_helper_interface $unit_helper, user $user)
	{
		$this->auth = $auth;
		$this->config = $config;
		$this->config_text = $config_text;
		$this->currency = $currency;
		$this->helper = $helper;
		$this->language = $language;
		$this->pkg_operator = $pkg_operator;
		$this->request = $request;
		$this->sub_operator = $sub_operator;
		$this->template = $template;
		$this->unit_helper = $unit_helper;
		$this->user = $user;
	}

	/**
	 * Set the phpBB installation path information.
	 *
	 * @param string $root_path The root phpBB path
	 * @param string $php_ext   The script file extension
	 */
	public function set_path_info($root_path, $php_ext)
	{
		$this->root_path = $root_path;
		$this->php_ext = $php_ext;
	}

	/**
	 * Handle the /groupsub/{name} route.
	 *
	 * @param string|null $name The unique identifier of a package
	 *
	 * @return Response A Symfony Response object
	 */
	public function handle($name)
	{
		if (!$this->config['stevotvr_groupsub_active'] && !$this->auth->acl_get('a_'))
		{
			throw new http_exception(404, 'PAGE_NOT_FOUND');
		}

		if ($this->user->data['user_id'] == ANONYMOUS)
		{
			$u_redirect = $this->helper->route('stevotvr_groupsub_main', array('name' => $name));
			redirect(append_sid($this->root_path . 'ucp.' . $this->php_ext, 'mode=login&amp;redirect=' . $u_redirect));
		}

		$this->template->assign_vars(array(
			'U_ACTION'	=> $this->helper->route('stevotvr_groupsub_main', array('name' => $name)),
		));

		$header_uid = $this->config['stevotvr_groupsub_header_bbcode_uid'];
		$header_bitfield = $this->config['stevotvr_groupsub_header_bbcode_bitfield'];
		$header_options = $this->config['stevotvr_groupsub_header_bbcode_options'];
		$header = generate_text_for_display($this->config_text->get('stevotvr_groupsub_header'), $header_uid, $header_bitfield, $header_options);

		$footer_uid = $this->config['stevotvr_groupsub_footer_bbcode_uid'];
		$footer_bitfield = $this->config['stevotvr_groupsub_footer_bbcode_bitfield'];
		$footer_options = $this->config['stevotvr_groupsub_footer_bbcode_options'];
		$footer = generate_text_for_display($this->config_text->get('stevotvr_groupsub_footer'), $footer_uid, $footer_bitfield, $footer_options);

		$this->template->assign_vars(array(
			'HEADER'	=> $header,
			'FOOTER'	=> $footer,
		));

		$term_id = $this->request->variable('term_id', 0);
		if ($term_id)
		{
			return $this->select_term($term_id);
		}

		return $this->list_packages($name);
	}

	/**
	 * Show the list of available packages.
	 *
	 * @param string|null $name The unique identifier of a package
	 *
	 * @return Response A Symfony Response object
	 */
	protected function list_packages($name)
	{
		$packages = $this->pkg_operator->get_packages($name);

		if (empty($packages))
		{
			throw new http_exception(404, 'GROUPSUB_NO_PACKAGES');
		}

		$this->template->assign_var('COLLAPSE_TERMS', $this->config['stevotvr_groupsub_collapse_terms']);

		$subscriptions = $this->sub_operator->get_user_subscriptions($this->user->data['user_id']);
		$warn = $this->config['stevotvr_groupsub_warn_time'] * 86400;

		foreach ($packages as $package)
		{
			$vars = array(
				'ID'	=> $package['package']->get_id(),
				'NAME'	=> $package['package']->get_name(),
				'DESC'	=> $package['package']->get_desc_for_display(),
			);

			if (isset($subscriptions[$package['package']->get_id()]))
			{
				$expires = $subscriptions[$package['package']->get_id()]->get_expire();

				$vars = array_merge($vars, array(
					'S_ACTIVE'	=> true,
					'S_WARNING'	=> $expires && (($expires - time()) < $warn),

					'EXPIRES'	=> $expires ? $this->user->format_date($expires) : 0,
				));
			}

			$this->template->assign_block_vars('package', $vars);

			foreach ($package['terms'] as $term)
			{
				$this->template->assign_block_vars('package.term', array(
					'ID'		=> $term->get_id(),
					'PRICE'		=> $this->currency->format_price($term->get_currency(), $term->get_price()),
					'LENGTH'	=> $term->get_length() ? $this->unit_helper->get_formatted_timespan($term->get_length()) : 0,
				));
			}
		}

		return $this->helper->render('@stevotvr_groupsub/package_list.html', $this->language->lang('GROUPSUB_PACKAGE_LIST'));
	}

	/**
	 * Show the details of a package term.
	 *
	 * @param int $term_id The term ID
	 *
	 * @return Response A Symfony Response object
	 */
	protected function select_term($term_id)
	{
		$term = $this->pkg_operator->get_package_term($term_id);
		if (!$term)
		{
			throw new http_exception(404, 'PAGE_NOT_FOUND');
		}

		$sandbox = $this->config['stevotvr_groupsub_pp_sandbox'];
		$client_id = $this->config[$sandbox ? 'stevotvr_groupsub_sb_client' : 'stevotvr_groupsub_pp_client'];
		$client_secret = $this->config[$sandbox ? 'stevotvr_groupsub_sb_secret' : 'stevotvr_groupsub_pp_secret'];

		$u_ppsdk = sprintf('https://www.paypal.com/sdk/js?client-id=%s&amp;locale=%s&amp;currency=%s', $client_id, $this->language->lang('GROUPSUB_PP_LOCALE'), $term['term']->get_currency());
		$u_create = $this->helper->route('stevotvr_groupsub_ppjs', array('action' => 'create'));
		$u_capture = $this->helper->route('stevotvr_groupsub_ppjs', array('action' => 'capture'));

		$paypal_config = json_encode(array(
			'u_create'	=> $u_create,
			'u_capture'	=> $u_capture,
			'term_id'	=> $term['term']->get_id(),
		));

		$this->template->assign_vars(array(
			'PP_ENABLED'	=> !empty($client_id) && !empty($client_secret),

			'PKG_NAME'		=> $term['package']->get_name(),
			'PKG_DESC'		=> $term['package']->get_desc_for_display(),
			'TERM_PRICE'	=> $this->currency->format_price($term['term']->get_currency(), $term['term']->get_price()),
			'TERM_LENGTH'	=> $term['term']->get_length() ? $this->unit_helper->get_formatted_timespan($term['term']->get_length()) : 0,

			'PAYPAL_CONFIG'	=> $paypal_config,

			'U_PPSDK'	=> $u_ppsdk,
		));

		if ($this->request->is_ajax())
		{
			confirm_box(false, ['GROUPSUB_CONFIRM', $term['package']->get_name()], '', 'select_term.html');
		}

		return $this->helper->render('@stevotvr_groupsub/select_term.html', $term['package']->get_name());
	}
}
