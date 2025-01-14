<?php

/**
 * Kunena Component
 *
 * @package         Kunena.Template.Aurelia
 * @subpackage      Layout.Rating
 *
 * @copyright       Copyright (C) 2008 - @currentyear@ Kunena Team. All rights reserved.
 * @license         https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link            https://www.kunena.org
 **/

namespace Kunena\Forum\Site;

\defined('_JEXEC') or die();

use Joomla\CMS\Language\Text;

if ($this->config->ratingEnabled && $this->category->allowRatings) :
    $this->addStyleSheet('rating.css');
    $this->addScript('rating.js');
    $this->addScript('krating.js');

    Text::script('COM_KUNENA_RATING_SUCCESS_LABEL');
    Text::script('COM_KUNENA_RATING_WARNING_LABEL');

    if ($this->topic->rating) :
        ?>
        <div id="krating-top"
             data-bs-toggle="tooltip" title="<?php echo Text::sprintf('COM_KUNENA_RATE_TOOLTIP', $this->topic->rating, $this->reviewCount); ?>"
             class="hasTooltip">
            <ul class="c-rating">
                <li class="c-rating__item is-active" data-index="0"></li>
                <li class="c-rating__item <?php echo $this->topic->rating >= 2 ? 'is-active' : ''; ?>"
                    data-index="1"></li>
                <li class="c-rating__item <?php echo $this->topic->rating >= 3 ? 'is-active' : ''; ?>"
                    data-index="2"></li>
                <li class="c-rating__item <?php echo $this->topic->rating >= 4 ? 'is-active' : ''; ?>"
                    data-index="3"></li>
                <li class="c-rating__item <?php echo $this->topic->rating >= 5 ? 'is-active' : ''; ?>"
                    data-index="4"></li>
            </ul>
        </div>
    <?php endif;
endif;
