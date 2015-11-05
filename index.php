<?php
// web/index.php
require_once __DIR__.'/vendor/autoload.php';

// make a storage directory and file if non exists
// create new directory with 744 permissions if it does not exist yet
// owner will be the user/group the PHP script is run under
$dir = 'data';
if ( !file_exists($dir) ) {
  mkdir ($dir, 0744);
  $file = fopen($dir . '/todos.json', 'w');

  //file_put_contents ($dir.'/test.txt', 'Hello File');
}

 


$app = new Silex\Application();

// app stuff
$app['debug'] = true;

//use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;
$app->register(new FormServiceProvider());
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
$app->register(new Silex\Provider\ValidatorServiceProvider());

// app/routes.php
// still not working despite autoloader.... WHY??????
//$app->mount("/todos", new \Sascha\Controller\Provider\Todo());

// integrate TWIG at the "views" directory
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));




$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('test.twig', array(
        'name' => $name,
    ));
});

$todos = array(
    1 => array(
    	'id'		=> 1,
        'title'     => 'Task Number 1',
        'body'      => 'Some text in here....',
        'done'		=> true,
    ),
    2 => array(
    	'id'		=> 2,
        'title'     => 'Task Number 2',
        'body'      => 'Some text in here....',
        'done'		=> false,
    ),
    3 => array(
    	'id'		=> 3,
        'title'     => 'Task Number 3',
        'body'      => 'Some text in here....',
        'done'		=> false,
    ),
    4 => array(
    	'id'		=> 4,
        'title'     => 'Task Number 4',
        'body'      => 'Some text in here....',
        'done'		=> true,
    ),
    5 => array(
    	'id'		=> 5,
        'title'     => 'Task Number 5',
        'body'      => 'Some text in here....',
        'done'		=> true,
    ),
);

$todos = json_decode(file_get_contents('todos.json'), true);


$app->get('/twig', function () use ($app, $todos) {
    $form = sas_loadForm('Todo', $app);
    //var_dump($form) . '<br>';
    
    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('index.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

$app->post('/twig', function (Request $request) use ($app, $todos) {
    $form = sas_loadForm('Todo', $app);
    //var_dump($form) . '<br>';
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        var_dump($data) . '<br>';
        $new_id = count($todos);
        $todos[] = array(
        				'id'	=> $new_id,
        				'title' => $data['Title'],
        				'body' 	=> $data['Body'],
        				'done' 	=> false 
        			);
        var_dump($todos) . '<br>';
        // do something with the data
        // save todos in a file
	    file_put_contents("todos.json",json_encode($todos));
        // redirect somewhere
        //return $app->redirect('twig');
    }
    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('index.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('test.twig', array(
        'name' => $name,
    ));
});

// MUST PASS THE $app into the function!!!!
$app->get('/todo/list', function (Silex\Application $app) use ($todos) {
    return $app['twig']->render('todo/list.twig', array(
        'todos' => $todos,
    ));
});

$app->match('/form', function (Request $request) use ($app) {
    // some default data for when the form is displayed the first time
    // ?? HOW DO I CLEAR DEFAULTS ON FOCUS ??
    $data = array(
        'title' => 'Your next Todo',
        'body' => 'I have to get some bananas for the monkeys...',
    );

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('title', 'text', array('attr' => array('class' => 'form-control', 'size' => 100)))
        ->add('body', 'textarea', array('attr' => array('class' => 'form-control', 'cols' => 100, 'rows' => 20)))
        ->getForm();

    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        // do something with the data

        // redirect somewhere
        return $app->redirect('todo/list');
    }

    // display the form
    return $app['twig']->render('components/form.twig', array('form' => $form->createView()));
});

function sas_loadForm($formName, Silex\Application $app) {
    switch ($formName) {
        case 'Todo':
            $data = array(
                'Title' => 'Your next Todo',
                'Body' => 'I have to get some bananas for the monkeys...',
            );

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('Title', 'text', array('attr' => array('class' => 'form-control')))
                ->add('Body', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => 5)))
                ->getForm();
        break;
        case 'Contact':
            $data = array(
                'title' => 'Contact Us',
                'body' => 'This is a message to you guys...',
            );

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('title', 'text', array('attr' => array('class' => 'form-control')))
                ->add('body', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => 5)))
                ->getForm();
        break;
    }

    // is that check enough????
    if ($form) {
        return $form;
    }
    else {
        return false;
    }
}



$app->run();