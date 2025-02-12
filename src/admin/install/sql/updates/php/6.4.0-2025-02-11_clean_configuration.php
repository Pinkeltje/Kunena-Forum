<?php
/**
 * Kunena Component
 *
 * @package        Kunena.Installer
 *
 * @copyright      Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license        https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link           https://www.kunena.org
 **/
defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Factory\KunenaFactory;

/**
 * Clean in the configuration the leftovers of previous parameters which has been deleted since
 * 
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.4.0
 */
function kunena_640_2025_02_11_clean_configuration($parent) {
	$config = KunenaFactory::getConfig();
	 
	if (isset($config->emailHeadersizey)) {    
	    unset($config->emailHeadersizey);
	}
	
	if (isset($config->emailHeadersizex)) {	    
	    unset($config->emailHeadersizex);
	}
	
	if (isset($config->board_title)) {	    
	    unset($config->board_title);
	}
	
	if (isset($config->board_offline)) {	    
	    unset($config->board_offline);
	}
	
	if (isset($config->offline_message)) {	    
	    unset($config->offline_message);
	}
	
	if (isset($config->userlist_posts)) {    
	    unset($config->userlist_posts);
	}
	
	if (isset($config->userlist_karma)) {	    
	    unset($config->userlist_karma);
	}
	
	// Save configuration
	$config->save();	

	return array('action' => '', 'name' => Text::_('COM_KUNENA_INSTALL_640_CLEAN_CONFIGURATION'), 'success' => true);
}
