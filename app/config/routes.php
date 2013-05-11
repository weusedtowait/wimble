<?php
return array(
    '_root_'  => 'welcome/index',  // The default route
    '_404_'   => 'welcome/404',    // The main 404 route

    'hello(/:name)?' => array('welcome/hello', 'name' => 'hello'),


	'_root_'  => 'views/index',  // The default route
	'_404_'   => 'views/404',    // The main 404 route
	'views(/:any)' => 'views/index/$1'
);