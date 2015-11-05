<?php
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

// routers and what they do
// would be great to separate the two......

// Todo List & Form
$app->get('/twig', function () use ($app) {
	$todos = json_decode(file_get_contents('data/todos.json'), true);
    $form = sas_loadForm('Todo', $app);
    
    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('index.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// Adding another Todo
$app->post('/twig', function (Request $request) use ($app) {
    $todos = json_decode(file_get_contents('data/todos.json'), true);
    $form = sas_loadForm('Todo', $app);
    //var_dump($form) . '<br>';
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        // calculate new ID
        $ids = array();
        foreach ($todos as $todo) {
        	$ids[] = $todo['id'];
        }
        if (empty($todos)) {
        	$new_id = 1;
        }
        else {
        	$new_id = max($ids)+1;
        }

        $todos[] = array(
        				'id'	=> $new_id,
        				'title' => $data['Title'],
        				'body' 	=> $data['Body'],
        				'done' 	=> false 
        			);
        
        // save todos in a file
	    file_put_contents("data/todos.json",json_encode($todos));
        // redirect somewhere
        return $app->redirect('twig');
    }
   
    // form must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('index.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// deleting a Todo
$app->post('/todo/delete', function (Request $request) use ($app) {
	$todos = json_decode(file_get_contents('data/todos.json'), true);
	$id = intval($request->request->get('id'));
	//var_dump($todos);
	foreach ($todos as $todo) {
		if ($todo['id'] === $id) {
			$todos = removeElementWithValue($todos, 'id', $id);
			file_put_contents("data/todos.json",json_encode($todos));
			return 'success';
		}
	}
	return 'error';
});

// marking Todo as done
$app->post('/todo/done', function (Request $request) use ($app) {
	$todos = json_decode(file_get_contents('data/todos.json'), true);
	$id = intval($request->request->get('id'));
	$done = strToBool($request->request->get('done'));
	//var_dump($todos);
	foreach ($todos as &$todo) {
		if ($todo['id'] === $id) {
			$todo['done'] = $done;
			file_put_contents("data/todos.json",json_encode($todos));
			return 'success';
		}
	}
	return 'error';
});		







// Everything below is learning curve!!!
/*

$app->get('/hello/{name}', function ($name) use ($app) {
    return 'Hello '.$app->escape($name);
});

$app->get('/twig/{name}', function ($name) use ($app) {
    return $app['twig']->render('test.twig', array(
        'name' => $name,
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
*/