<?php
/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Controller.Message
 *
 * @copyright       Copyright (C) 2008 - 2021 Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Controller\Message\Item\Actions;

\defined('_JEXEC') or die();

use Exception;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;
use Kunena\Forum\Libraries\Controller\KunenaControllerDisplay;
use Kunena\Forum\Libraries\Factory\KunenaFactory;
use Kunena\Forum\Libraries\Forum\Message\KunenaMessage;
use Kunena\Forum\Libraries\Forum\Topic\KunenaTopic;
use Kunena\Forum\Libraries\Layout\KunenaLayout;
use Kunena\Forum\Libraries\Login\KunenaLogin;
use Kunena\Forum\Libraries\Route\KunenaRoute;
use Kunena\Forum\Libraries\User\KunenaUserHelper;

/**
 * Class ComponentKunenaControllerMessageItemActionsDisplay
 *
 * @since   Kunena 4.0
 */
class MessageItemActionsDisplay extends KunenaControllerDisplay
{
	/**
	 * @var     KunenaTopic
	 * @since   Kunena 6.0
	 */
	public $topic;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	public $message;

	/**
	 * @var     array
	 * @since   Kunena 6.0
	 */
	public $messageButtons;

	/**
	 * @var     string
	 * @since   Kunena 6.0
	 */
	protected $name = 'Message/Item/Actions';

	/**
	 * Prepare message actions display.
	 *
	 * @return  boolean|false
	 *
	 * @throws  null
	 * @throws  Exception
	 * @since   Kunena 6.0
	 */
	protected function before()
	{
		parent::before();

		$mesid = $this->input->getInt('mesid');
		$me    = KunenaUserHelper::getMyself();

		$this->message  = KunenaMessage::getInstance($mesid);
		$this->topic    = $this->message->getTopic();
		$this->category = $this->topic->getCategory();

		$id     = $this->message->thread;
		$catid  = $this->message->catid;
		$token  = Session::getFormToken();
		$Itemid = KunenaRoute::fixMissingItemID();

		$task   = "index.php?option=com_kunena&view=topic&catid={$catid}&id={$id}&mesid={$mesid}&Itemid={$Itemid}&{$token}=1";
		$layout = "index.php?option=com_kunena&view=topic&catid={$catid}&id={$id}&mesid={$mesid}&Itemid={$Itemid}";

		$this->messageButtons = new Registry;
		$this->message_closed = null;

		$ktemplate     = KunenaFactory::getTemplate();
		$fullactions   = $ktemplate->params->get('fullactions');
		$topicicontype = $ktemplate->params->get('topicicontype');

		$button = $fullactions ? true : false;

		$this->quickReply = null;

		if ($this->config->readOnly)
		{
			return false;
		}

		// Reply / Quote
		if ($this->message->isAuthorised('reply'))
		{
			$this->quickReply = $this->config->quickReply;

			if ($topicicontype == 'B5' && !$fullactions)
			{
				$this->messageButtons->set(
					'reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'pencil')
				);
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set(
					'reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'fa fa-reply')
				);
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set(
					'reply',
					$this->getButton(sprintf($layout, 'reply'), 'reply', 'message', 'communication', 'reply', $button, 'kicon-reply')
				);
			}
			else
			{
				$this->messageButtons->set(
					'reply',
					$this->getButton($layout, 'reply', 'message', 'communication', 'reply', $button)
				);
			}

			if ($me->exists() && $this->config->quickReply)
			{
				if ($topicicontype == 'B5')
				{
					$this->messageButtons->set(
						'quickReply',
						$this->getButton(sprintf($layout, 'reply'), 'quickReply', 'message', 'communication', "kreply{$mesid}", 'reply', 'reply')
					);
				}
				elseif ($topicicontype == 'fa')
				{
					$this->messageButtons->set(
						'quickReply',
						$this->getButton(sprintf($layout, 'reply'), 'quickReply', 'message', 'communication', "kreply{$mesid}", 'reply', 'fa fa-reply')
					);
				}
				elseif ($topicicontype == 'image')
				{
					$this->messageButtons->set(
						'quickReply',
						$this->getButton(sprintf($layout, 'reply'), 'quickReply', 'message', 'communication', "kreply{$mesid}", 'reply', 'kicon-reply')
					);
				}
				else
				{
					$this->messageButtons->set(
						'quickReply',
						$this->getButton(sprintf($layout, 'reply'), 'quickReply', 'message', 'communication', "kreply{$mesid}", 'reply')
					);
				}
			}

			if ($topicicontype == 'B5' && !$fullactions)
			{
				$this->messageButtons->set(
					'quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'chat')
				);
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set(
					'quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'fa fa-quote-left')
				);
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set(
					'quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button, 'kicon-quote')
				);
			}
			else
			{
				$this->messageButtons->set(
					'quote',
					$this->getButton(sprintf($layout, 'reply&quote=1'), 'quote', 'message', 'communication', 'quote', $button)
				);
			}
		}
		elseif (!$me->isModerator($this->topic->getCategory()))
		{
			// User is not allowed to write a post.
			$this->message_closed = $this->topic->locked ? Text::_('COM_KUNENA_POST_LOCK_SET') :
				($me->exists() ? Text::_('COM_KUNENA_REPLY_USER_REPLY_DISABLED') : ' ');
		}

		$login = KunenaLogin::getInstance();

		if (!$this->message->isAuthorised('reply') && !$this->message_closed && $login->enabled() && !$this->message->hold
			&& !$this->config->readOnly || !$this->message->isAuthorised('reply') && !$this->topic->locked && $login->enabled()
			&& !$me->userid && !$this->message->hold && !$this->config->readOnly
		)
		{
			$loginurl  = Route::_('index.php?option=com_users&view=login&return=' . base64_encode((string) Uri::getInstance()));
			$logintext = sprintf('<a class="btn-link" href="%s" rel="nofollow">%s</a>', $loginurl, Text::_('JLOGIN'));

			if ($login->getRegistrationUrl())
			{
				$register = ' ' . Text::_('COM_KUNENA_LOGIN_OR') . ' <a class="btn-link" href="' . $login->getRegistrationUrl()
					. '">' . Text::_('COM_KUNENA_PROFILEBOX_CREATE_ACCOUNT') . '</a>';
			}
			else
			{
				$register = '';
			}

			echo '<p>' . Text::sprintf('COM_KUNENA_LOGIN_PLEASE', $logintext, $register) . '</p>';
		}

		// Thank you.
		if (isset($this->message->thankyou))
		{
			if ($this->message->isAuthorised('thankyou') && !\array_key_exists($me->userid, $this->message->thankyou))
			{
				if ($topicicontype == 'B5')
				{
					$this->messageButtons->set(
						'thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'plus')
					);
				}
				elseif ($topicicontype == 'fa')
				{
					$this->messageButtons->set(
						'thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'fa fa-thumbs-up')
					);
				}
				elseif ($topicicontype == 'image')
				{
					$this->messageButtons->set(
						'thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false, 'kicon-thumbs-up')
					);
				}
				else
				{
					$this->messageButtons->set(
						'thankyou',
						$this->getButton(sprintf($task, 'thankyou'), 'thankyou', 'message', 'user', 'thankyou', false)
					);
				}
			}
		}

		// Unthank you

		if ($this->config->showThankYou)
		{
			if ($this->message->isAuthorised('unthankyou') && \array_key_exists($me->userid, $this->message->thankyou))
			{
				if ($topicicontype == 'B5')
				{
					$this->messageButtons->set(
						'unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'dash')
					);
				}
				elseif ($topicicontype == 'fa')
				{
					$this->messageButtons->set(
						'unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'fa fa-thumbs-down')
					);
				}
				elseif ($topicicontype == 'image')
				{
					$this->messageButtons->set(
						'unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false, 'kicon-thumbs-down')
					);
				}
				else
				{
					$this->messageButtons->set(
						'unthankyou',
						$this->getButton(sprintf($task, 'unthankyou&userid=' . $me->userid), 'unthankyou', 'message', 'user', 'unthankyou', false)
					);
				}
			}
		}

		// Report this.
		if ($this->config->reportMsg && $me->exists())
		{
			if ($me->isModerator($this->topic->getCategory()) || $this->config->userReport
				|| !$this->config->userReport && $me->userid != $this->message->userid)
			{
				if ($topicicontype == 'B5')
				{
					$icon = 'alert-octagon';
				}
				elseif ($topicicontype == 'fa')
				{
					$icon = 'fa fa-exclamation';
				}
				elseif ($topicicontype == 'image')
				{
					$icon = 'kicon-report';
				}
				else
				{
					$icon = '';
				}

				$this->messageButtons->set(
					'report',
					$this->getButton(sprintf($layout, '#report' . $mesid . ''), 'report', 'message', 'user', 'btn_report', $button, $icon)
				);
			}
		}

		// Moderation and own post actions.
		if ($this->message->isAuthorised('edit'))
		{
			if ($me->userid == $this->message->userid && $this->config->userEdit)
			{
				// Allow edit message when enabled.
				$this->message_closed = null;
			}

			if ($topicicontype == 'B5' && !$fullactions)
			{
				$this->messageButtons->set(
					'edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'pencil')
				);
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set(
					'edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'fa fa-edit')
				);
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set(
					'edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button, 'kicon-edit')
				);
			}
			else
			{
				$this->messageButtons->set(
					'edit',
					$this->getButton(sprintf($layout, 'edit'), 'edit', 'message', 'moderation', 'edit', $button)
				);
			}
		}

		if ($this->message->isAuthorised('move'))
		{
			if ($topicicontype == 'B5' && !$fullactions)
			{
				$this->messageButtons->set(
					'moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'arrow-left-right')
				);
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set(
					'moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'fa fa-random')
				);
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set(
					'moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button, 'kicon-move')
				);
			}
			else
			{
				$this->messageButtons->set(
					'moderate',
					$this->getButton(sprintf($layout, 'moderate'), 'moderate', 'message', 'moderation', 'edit', $button)
				);
			}
		}

		if ($this->message->hold == 1)
		{
			if ($this->message->isAuthorised('approve'))
			{
				if ($topicicontype == 'B5' && !$fullactions)
				{
					$this->messageButtons->set(
						'publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'check-circle')
					);
				}
				elseif ($topicicontype == 'fa' && !$fullactions)
				{
					$this->messageButtons->set(
						'publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'fas fa-check-circle')
					);
				}
				elseif ($topicicontype == 'image' && !$fullactions)
				{
					$this->messageButtons->set(
						'publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button, 'kicon-approve')
					);
				}
				else
				{
					$this->messageButtons->set(
						'publish',
						$this->getButton(sprintf($task, 'approve'), 'approve', 'message', 'moderation', 'approve', $button)
					);
				}
			}

			if ($topicicontype == 'B5' && !$fullactions)
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'trash')
				);
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'fa fa-trash')
				);
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'kicon-delete')
				);
			}
			else
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button)
				);
			}
		}
		elseif ($this->message->hold == 2 || $this->message->hold == 3)
		{
			if ($this->message->isAuthorised('undelete'))
			{
				if ($topicicontype == 'B5' && !$fullactions)
				{
					$this->messageButtons->set(
						'undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'box-arrow-up')
					);
				}
				elseif ($topicicontype == 'fa' && !$fullactions)
				{
					$this->messageButtons->set(
						'undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'fas fa-check-circle')
					);
				}
				elseif ($topicicontype == 'image' && !$fullactions)
				{
					$this->messageButtons->set(
						'undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button, 'kicon-undelete')
					);
				}
				else
				{
					$this->messageButtons->set(
						'undelete',
						$this->getButton(sprintf($task, 'undelete'), 'undelete', 'message', 'moderation', 'undelete', $button)
					);
				}
			}

			if ($this->message->getTopic()->isAuthorised('permdelete'))
			{
				if ($topicicontype == 'B5' && !$fullactions)
				{
					$this->messageButtons->set(
						'permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'trash-fill')
					);
				}
				elseif ($topicicontype == 'fa' && !$fullactions)
				{
					$this->messageButtons->set(
						'permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'fa fa-exclamation')
					);
				}
				elseif ($topicicontype == 'image' && !$fullactions)
				{
					$this->messageButtons->set(
						'permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button, 'kicon-permdelete')
					);
				}
				else
				{
					$this->messageButtons->set(
						'permdelete',
						$this->getButton(sprintf($task, 'permdelete'), 'permdelete', 'message', 'moderation', 'permdelete', $button)
					);
				}
			}
		}
		elseif ($this->message->isAuthorised('delete'))
		{
			if ($topicicontype == 'B5' && !$fullactions)
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'trash')
				);
			}
			elseif ($topicicontype == 'fa' && !$fullactions)
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'fa fa-trash')
				);
			}
			elseif ($topicicontype == 'image' && !$fullactions)
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button, 'kicon-delete')
				);
			}
			else
			{
				$this->messageButtons->set(
					'delete',
					$this->getButton(sprintf($task, 'delete'), 'delete', 'message', 'moderation', 'delete', $button)
				);
			}
		}

		// Show admins the IP address of the user.
		if ($this->category->isAuthorised('admin')
			|| ($this->category->isAuthorised('moderate') && !$this->config->hideIp))
		{
			if (!empty($this->message->ip))
			{
				$this->ipLink = '<a href="https://dnslytics.com/ip/' . $this->message->ip
					. '" target="_blank" rel="nofollow noopener noreferrer"> IP: ' . $this->message->ip . '</a>';
			}
			else
			{
				$this->ipLink = '&nbsp;';
			}
		}

		PluginHelper::importPlugin('kunena');

		Factory::getApplication()->triggerEvent('onKunenaGetButtons', ['message.action', $this->messageButtons, $this]);
	}

	/**
	 * Get button.
	 *
	 * @param   string  $url     Target link (do not route it).
	 * @param   string  $name    Name of the button.
	 * @param   string  $scope   Scope of the button.
	 * @param   string  $type    Type of the button.
	 * @param   int     $id      Id of the button.
	 * @param   bool    $normal  Define if the button will have the class btn or btn-small
	 *
	 * @param   string  $icon    icon
	 *
	 * @return  KunenaLayout
	 *
	 * @throws  Exception
	 * @throws  null
	 * @since   Kunena 6.0
	 */
	public function getButton($url, $name, $scope, $type, $id = null, $normal = true, $icon = '')
	{
		return KunenaLayout::factory('Widget/Button')
			->setProperties(
				['url'  => $url, 'name' => $name, 'scope' => $scope,
				 'type' => $type, 'id' => 'btn_' . $id, 'normal' => $normal, 'icon' => $icon,]
			);
	}
}
