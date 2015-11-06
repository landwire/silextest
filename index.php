<?php
// web/index.php
require_once __DIR__.'/vendor/autoload.php';
require_once __DIR__.'/helpers/helpers.php';
require_once __DIR__.'/classes/todos.php';

// make a storage directory and file if non exists
// create new directory with 744 permissions if it does not exist yet
// owner will be the user/group the PHP script is run under
$dir = 'data';
if ( !file_exists($dir) ) {
  mkdir ($dir, 0744);
  $file = fopen($dir . '/todos.json', 'w');
}

$app = new Silex\Application();

// app stuff
$app['debug'] = true;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
$app->register(new Silex\Provider\ValidatorServiceProvider());

// integrate TWIG at the "views" directory
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));

// load the form helper
require_once __DIR__.'/helpers/forms.php';

// load router
require_once __DIR__.'/router/router.php';

// app/routes.php
// still not working despite autoloader.... WHY??????
//$app->mount("/todos", new \Sascha\Controller\Provider\Todo());

$app->run();