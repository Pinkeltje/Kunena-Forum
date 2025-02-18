<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Site
 * @subpackage      Layout.Announcement.Edit
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site\Layout\Announcement;

\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Kunena\Forum\Libraries\Forum\Announcement\KunenaAnnouncement;
use Kunena\Forum\Libraries\Layout\KunenaLayout;

/**
 * KunenaLayoutAnnouncementEdit
 *
 * @since   Kunena 4.0
 */
class AnnouncementEdit extends KunenaLayout
{
    /**
     * @var     KunenaAnnouncement
     * @since   Kunena 6.0
     */
    public $announcement;

    public $output;

    public $user;

    public $headerText;

    public $pagination;

    public $config;

    /**
     * Method to create an input in function of name given
     *
     * @param   string  $name        Name of input to create
     * @param   string  $attributes  Attibutes to be added to input
     * @param   int     $id          Id to be added to the input
     *
     * @return  string
     *
     * @since   Kunena 6.0
     */
    public function displayInput($name, $attributes = '', $id = false)
    {
        switch ($name) {
            case 'id':
                return '<input type="hidden" name="id" value="' . \intval($this->announcement->id) . '" />';
            case 'title':
                return '<input type="text" name="title" ' . $attributes . ' value="' . $this->escape($this->announcement->title) . '"/>';
            case 'sdescription':
                return '<textarea name="sdescription" ' . $attributes . '>' . $this->escape($this->announcement->sdescription) . '</textarea>';
            case 'description':
                return '<textarea name="description" ' . $attributes . '>' . $this->escape($this->announcement->description) . '</textarea>';
            case 'created':
                return '<input type="text" class="span12" name="created" data-date-format="' . $this->config->datePickerFormat . '" value="' . $this->escape($this->announcement->created) . '">' . $attributes;
            case 'publish_up':
                return '<input type="text" class="span12" name="publish_up" data-date-format="' . $this->config->datePickerFormat . '" value="' . $this->escape($this->announcement->publish_up) . '">' . $attributes;
            case 'publish_down':
                return '<input type="text" class="span12" name="publish_down" data-date-format="' . $this->config->datePickerFormat . '" value="' . $this->escape($this->announcement->publish_down) . '">' . $attributes;
            case 'showdate':
                $options   = [];
                $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_NO'));
                $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_YES'));

                return HTMLHelper::_('select.genericlist', $options, 'showdate', $attributes, 'value', 'text', $this->announcement->showdate, $id);
            case 'published':
                $options   = [];
                $options[] = HTMLHelper::_('select.option', '0', Text::_('COM_KUNENA_NO'));
                $options[] = HTMLHelper::_('select.option', '1', Text::_('COM_KUNENA_YES'));

                return HTMLHelper::_('select.genericlist', $options, 'published', $attributes, 'value', 'text', $this->announcement->published, $id);
        }

        return '';
    }
}
