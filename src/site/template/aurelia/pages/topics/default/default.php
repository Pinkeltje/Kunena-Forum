<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Pages.Topics
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

defined('_JEXEC') or die();

// $this should be an object of KunenaLayoutPage
$content = $this->execute('Topic/Listing/Recent')
    ->setLayout('recent');

$this->addBreadcrumb(
    $content->headerText,
    'index.php?option=com_kunena&view=topics&layout=default'
);

echo $content;
echo $this->subRequest('Widget/WhoIsOnline');
echo $this->subRequest('Widget/Statistics');
