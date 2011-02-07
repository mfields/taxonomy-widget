===Plugin Name===
Taxonomy Widget

Contributors: mfields
Donate link: http://wordpress.mfields.org/donate/
Tags: taxonomy, tag, category, widget, cloud, dropdown
Requires at least: 2.9.2
Tested up to: 3.0
Stable tag: trunk

Creates widgets for any taxonomy. Display terms as a ordered list, unordered list, term cloud or dropdown menu.

==Description==
The Taxonomy Widget Plugin enables users of all skill levels to create widgets in their sidebar that display all terms of any given post taxonomy including tags and categories. Users can choose between 3 different templates including two types of lists, a term cloud or a dropdown menu.


= Options =
__Title__ - You can enter a custom title for your widget in this text input. If you leave this field blank, The name of the Taxonomy will be used. If you do not want a title displayed at all, you can toggle this by un-checking the *Display title* box under *Advanced Options*.

__Taxonomy__ - You can select the taxonomy whose terms you would like displayed by selecting it from the dropdown menu.

__Template__ - Select a template for your terms by selecting one of the radio buttons in the *Display Taxonomy As:* section.

__Display title__ - If checked the title will be displayed. Un-checking this option will hide the title. Defaults to checked.

__Show post counts__ - If checked, the number of posts associated with each term will be displayed to the right of the term name in the template. This option has no effect in the *cloud* template.

__Show hierarchy__ - If checked, the terms will be indented from the left if they are children of other terms. This option has no effect in the *cloud* template.


== Screenshots ==
1. Administration view.


= Support =
If you find that this plugin is has a bug, does not play nicely with other plugins or if you have a suggestion or comment, please <a href="http://wordpress.org/tags/taxonomy-widget?forum_id=10#postform">use this link to add a new thread to the WordPress Support Forum</a>


==Installation==
1. Download
1. Unzip the package and upload to your /wp-content/plugins/ directory.
1. Log into WordPress and navigate to the "Plugins" panel.
1. Activate the plugin.


==Changelog==

= 0.4 =
* Dropped support for 2.9 branch.
* Removed mfields_walk_taxonomy_dropdown_tree().
* Removed mfields_dropdown_taxonomy_terms().
* Removed global variables.
* Moved javascript listeners into mfields_taxonomy_widget class.
* Create mfields_taxonomy_widget::clean_args() to sanitize user input.
* Removed mfields_taxonomy_widget::sanitize_template().
* Removed mfields_taxonomy_widget::sanitize_taxonomy().
* Tag clouds will now only display if their setting allow.
* Tested with post formats.
* Removed mfields_taxonomy_widget::get_query_var_name() using $taxonomy->query_var instead.

= 0.3 =
* Added filters for show_option_none text in dropdown.

= 0.2.2 =
* Don't remember. Sorry...

= 0.2.1 =
* __BUGFIX:__ Dropdown now displays 'Please Choose' on non-taxonomy views.

= 0.2 =
* Now compatible with WordPress Version 2.9.2.

= 0.1 =
* Original Release - Works With: wp 3.0 Beta 2.
