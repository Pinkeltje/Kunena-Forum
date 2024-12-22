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

use Joomla\CMS\Factory;
use Kunena\Forum\Libraries\User\KunenaUserSocials;

/**
 * @param $parent
 *
 * @return array
 * @throws Exception
 * @since Kunena 6.4.0
 */
function kunena_640_2024_12_20_add_fa_socials($parent) { 
    $db     = Factory::getContainer()->get('DatabaseDriver');
    $query  = $db->createQuery()
    ->select('socials')
    ->from($db->quoteName('#__kunena_users'))
    ->where($db->quoteName('banned') . '= ' . $db->quote('1000-01-01 00:00:00')
        );
    $db->setQuery($query);
    $results = (array) $db->loadObjectList();
    
    foreach($results as $result) {
        $socials = KunenaUserSocials::getInstance($result->userid, false);
        
        if (isset($result->x_social)) {
            $socials->x_social->fa = 'fa-brands fa-x-twitter';
        }
        
        if (isset($result->facebook)) {
            $socials->facebook->fa = 'fa-brands fa-facebook';
        }
        
        if (isset($result->linkedin)) {
            $socials->linkedin->fa = 'fa-brands fa-linkedin';
        }
        
        if (isset($result->linkedin_company)) {
            $socials->linkedin_company->fa = 'fa-brands fa-linkedin';
        }
        
        if (isset($result->digg)) {
            $socials->digg->fa = 'fa-brands fa-digg';
        }
        
        if (isset($result->skype)) {
            $socials->skype->fa = 'fa-brands fa-skype';
        }
        
        if (isset($result->yim)) {
            $socials->yim->fa = 'fa-brands fa-yahoo';
        }
        
        if (isset($result->google)) {
            $socials->google->fa = 'fa-brands fa-google';
        }
        
        if (isset($result->github)) {
            $socials->github->fa = 'fa-brands fa-github';
        }
        
        if (isset($result->microsoft)) {
            $socials->microsoft->fa = 'fa-brands fa-microsoft';
        }
        
        if (isset($result->flickr)) {
            $socials->flickr->fa = 'fa-brands fa-flickr';
        }
        
        if (isset($result->instagram)) {
            $socials->instagram->fa = 'fa-brands fa-instagram';
        }
        
        if (isset($result->weibo)) {
            $socials->weibo->fa = 'fa-brands fa-weibo';
        }
        
        if (isset($result->vk)) {
            $socials->vk->fa = 'fa-brands fa-vk';
        }
        
        if (isset($result->telegram)) {
            $socials->telegram->fa = 'fa-brands fa-telegram';
        }
        
        if (isset($result->apple)) {
            $socials->apple->fa = 'fa-brands fa-apple';
        }
        
        if (isset($result->vimeo)) {
            $socials->vimeo->fa = 'fa-brands fa-vimeo';
        }
        
        if (isset($result->whatsapp)) {
            $socials->whatsapp->fa = 'fa-brands fa-whatsapp';
        }
        
        if (isset($result->youtube)) {
            $socials->youtube->fa = 'fa-brands fa-youtube';
        }
        
        if (isset($result->pinterest)) {
            $socials->pinterest->fa = 'fa-brands fa-pinterest';
        }
        
        if (isset($result->reddit)) {
            $socials->reddit->fa = 'fa-brands fa-reddit';
        }
        
        if (isset($result->bsky_app)) {
            $socials->bluesky_app->fa = 'fa-brands fa-bluesky';
        }
        
        $socials->save();
    }
}
