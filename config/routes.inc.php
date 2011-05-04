<?php
// Add your routes here
//
// A route is a regular expression and handler name
// For example:
//
//   $GLOBALS['ROUTES']['~^/users~'] = "users";
//
// By default, we've created a root route, that responds to a request for "/"
//
$GLOBALS['ROUTES']['~^GET/([?].*)?$~'] = "root";
//
// If you put capturing parentheses in the regexp, then those will be available
// to your handler through `request()->param()`.
// For example:
//
//   $GLOBALS['ROUTES']['~^/users/(?P<user_id>\d+)~'] = "users";
//
// Will make an id available in `request()->param('user_id')`.
//
// Routes are resolved in the order that they are defined, so if an
// input matches multiple routes, it will go with the first defined one.
//
// Routes can also be provided from plugins.
//
// To see all routes and what they resolve to, run the script `scripts/routes`

$GLOBALS['ROUTES']['~^POST/([?].*)?$~'] = "root";
$GLOBALS['ROUTES']['~^/page_edit~'] = "page_edit";
$GLOBALS['ROUTES']['~^/new_page~'] = "page_new";
$GLOBALS['ROUTES']['~^/page~'] = "page";

// Add your url helpers here
function root_url() {
  return $GLOBALS['HREF_BASE'] . '/';
}

function page_url($filename) {
  return root_url() . 'page?filename=' . $filename;
}

function page_edit_url($filename) {
  return root_url() . 'page_edit?filename=' . $filename;
}

function page_new_url() {
  return root_url() . 'new_page';
}
