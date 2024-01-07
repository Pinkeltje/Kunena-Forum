/**
 * Kunena Component
 * @package Kunena.Template.Aurelia
 *
 * @copyright     Copyright (C) 2008 - 2024 Kunena Team. All rights reserved.
 * @license https://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @link https://www.kunena.org
 **/

jQuery(document).ready(function ($) {

    /* Provide autocomplete user list in search form and in user list */
    var tribute = new Tribute({
        collection: []
    });

    tribute.attach(document.getElementById("kusersearch"));

	/* Hide search form when there are search results found */
	if ($('#kunena_search_results').is(':visible')) {
		$('#search').collapse("hide");
	}

	if (jQuery.fn.datepicker !== undefined) {
		jQuery("#searchatdate.input-group.date").datepicker({
			orientation: "top auto",
			language: "kunena",
		});
	}
});
