Taxonomy Widget
===============

By [Michael Fields](http://wordpress.mfields.org/)

Tested up to WordPress version 3.2.1


About
-----

The Taxonomy Widget Plugin enables users to create widgets in their sidebar that display all terms of any given taxonomy. Users can choose between 3 different templates including two types of lists, a term cloud or a dropdown menu.


Options
-------

* __Title__ - You can enter a custom title for your widget in this text input. If you leave this field blank, The name of the taxonomy will be used. If you do not want a title displayed at all, you can toggle this by un-checking the *Display title* box under *Advanced Options*.

* __Taxonomy__ - You can select the taxonomy whose terms you would like displayed by selecting it from the dropdown menu.

* __Template__ - Select a template for your terms by selecting one of the radio buttons in the *Display Taxonomy As* section.

* __Display title__ - If checked the title will be displayed. Un-checking this option will hide the title. Defaults to checked.

* __Show post counts__ - If checked, the number of posts associated with each term will be displayed to the right of the term name in the template. This option has no effect in the *cloud* template.

* __Show hierarchy__ - If checked, the terms will be indented from the left if they are children of other terms. This option has no effect in the *cloud* template.


Changelog
---------

__0.6.1__

* Set value of the "taxonomies" property a bit later in the action sequence. 

__0.6__

* Cleanup.
* Provide alternative default if categories are disabled.
* Do not register widget if no taxonomies are registered.

__0.5.1__

* stupid comma ...
* another stupid comma !!!

__0.5__

* Better escaping throughout.
* Use get_term_link() for javascript redircts.

__0.4__

* Never officially released.
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

__0.3__

* Added filters for show_option_none text in dropdown.

__0.2.2__

* Don't remember. Sorry...

__0.2.1__

* __BUGFIX:__ Dropdown now displays 'Please Choose' on non-taxonomy views.

__0.2__

* Now compatible with WordPress Version 2.9.2.

__0.1__

* Original Release - Works With: wp 3.0 Beta 2.
