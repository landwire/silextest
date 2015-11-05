<?php
// web/index.php
require_once __DIR__.'/vendor/autoload.php';

$app = new Silex\Application();

// app stuff
$app['debug'] = true;

//use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
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
    ),
    2 => array(
    	'id'		=> 3,
        'title'     => 'Task Number 2',
        'body'      => 'Some text in here....',
    ),
    3 => array(
    	'id'		=> 5,
        'title'     => 'Task Number 3',
        'body'      => 'Some text in here....',
    ),
    4 => array(
    	'id'		=> 7,
        'title'     => 'Task Number 4',
        'body'      => 'Some text in here....',
    ),
    5 => array(
    	'id'		=> 11,
        'title'     => 'Task Number 1',
        'body'      => 'Some text in here....',
    ),
);


$app->match('/twig', function (Request $request) use ($app, $todos) {
    $form = sas_loadForm('Todo', $app);
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();

        // do something with the data

        // redirect somewhere
        return $app->redirect('twig');
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
        'Title' => 'Your next Todo',
        'Body' => 'I have to get some bananas for the monkeys...',
    );

    $form = $app['form.factory']->createBuilder('form', $data)
        ->add('Title', 'text', array('attr' => array('class' => 'form-control', 'size' => 100)))
        ->add('Body', 'textarea', array('attr' => array('class' => 'form-control', 'cols' => 100, 'rows' => 20)))
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
                'Title' => 'Contact Us',
                'Body' => 'This is a message to you guys...',
            );

            $form = $app['form.factory']->createBuilder('form', $data)
                ->add('Title', 'text', array('attr' => array('class' => 'form-control')))
                ->add('Body', 'textarea', array('attr' => array('class' => 'form-control', 'rows' => 5)))
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