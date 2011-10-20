<?php
/**
 *	This file was automaticaly generated using command line: 
 *	route_mapper.php src/main/php/YOUR_PROJECT/controller/resource/ src/main/php/YOUR_PROJECT/routes.php
 * 
 * 	Use the same command to update it.
 */

return array(
'PostResource' => array (
  'buildPath' => '/post/{year}/{month}/{day}',
  'matchPath' => '/^\\/post\\/(?<year>\\d{4})\\/(?<month>\\d{2})\\/(?<day>\\d{2})$/',
  'GET' => 
  array (
    0 => 
    array (
      'methodName' => 'doGet',
      'consumes' => NULL,
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/rdf+xml',
      ),
    ),
  ),
  'POST' => 
  array (
    0 => 
    array (
      'methodName' => 'doPost',
      'consumes' => 
      array (
        0 => 'application/x-www-form-urlencoded',
      ),
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/json',
      ),
    ),
  ),
),
'PostRandomResource' => array (
  'buildPath' => '/post/random',
  'matchPath' => '/^\\/post\\/random$/',
  'GET' => 
  array (
    0 => 
    array (
      'methodName' => 'doGet',
      'consumes' => NULL,
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/rdf+xml',
      ),
    ),
  ),
  'POST' => 
  array (
    0 => 
    array (
      'methodName' => 'doPost',
      'consumes' => 
      array (
        0 => 'application/x-www-form-urlencoded',
      ),
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/json',
      ),
    ),
  ),
),
'PostListResource' => array (
  'buildPath' => '/posts',
  'matchPath' => '/^\\/posts$/',
  'GET' => 
  array (
    0 => 
    array (
      'methodName' => 'doGet',
      'consumes' => NULL,
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/rdf+xml',
      ),
    ),
  ),
  'POST' => 
  array (
    0 => 
    array (
      'methodName' => 'doPost',
      'consumes' => 
      array (
        0 => 'application/x-www-form-urlencoded',
      ),
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/json',
      ),
    ),
  ),
),
'FrontPageResource' => array (
  'buildPath' => '/',
  'matchPath' => '/^\\/$/',
  'GET' => 
  array (
    0 => 
    array (
      'methodName' => 'doGet',
      'consumes' => NULL,
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/rdf+xml',
      ),
    ),
  ),
  'POST' => 
  array (
    0 => 
    array (
      'methodName' => 'doPost',
      'consumes' => 
      array (
        0 => 'application/x-www-form-urlencoded',
      ),
      'produces' => 
      array (
        0 => 'text/html',
        1 => 'application/json',
      ),
    ),
  ),
),
'AdminFrontPageResource' => array (
  'buildPath' => '/admin',
  'matchPath' => '/^\\/admin$/',
  'GET' => 
  array (
    0 => 
    array (
      'methodName' => 'rdf',
      'consumes' => NULL,
      'produces' => 
      array (
        0 => 'application/rdf+xml',
      ),
    ),
    1 => 
    array (
      'methodName' => 'doGet',
      'consumes' => NULL,
      'produces' => NULL,
    ),
  ),
  'POST' => 
  array (
    0 =>
    array (
      'methodName' => 'saveModel',
      'consumes' =>
      array (
        0 => 'application/x-www-form-urlencoded',
      ),
      'produces' =>
      array (
        0 => 'text/html',
      ),
    ),
    1 => 
    array (
      'methodName' => 'doPost',
      'consumes' => NULL,
      'produces' => NULL,
    ),
  ),
  'DELETE' => 
  array (
    0 => 
    array (
      'methodName' => 'doDelete',
      'consumes' => NULL,
      'produces' => NULL,
    ),
  ),
),
);

