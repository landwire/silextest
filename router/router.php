<?php
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

// routers and what they do
// would be great to separate the two......

// Todo List & Form
$app->get('/twig', function () use ($app) {
	$todos = json_decode(file_get_contents('data/todos.json'), true);
    $data = array(
                'Title' => 'Your next Todo',
                'Body' => 'I have to get some bananas for the monkeys...',
            );
    $form = sas_loadForm('Todo', $data, $app);
    
    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('index.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// Processing the form
$app->post('/twig', function (Request $request) use ($app) {
    $form = sas_loadForm('Todo', $data, $app);
    //var_dump($form) . '<br>';
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        
        // create new todo
        $todos = Todos::create_todo($data);

        // save todos
        $is_saved = Todos::save_todos($todos);
        
        if ($is_saved) {
            // redirect somewhere
            return $app->redirect('twig');
        }
        else {
            return 'error';
        }
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
	$todos = Todos::get_todos();
	$id = intval($request->request->get('id'));
	
    // delete todo returns new array of todos
    $todos = Todos::delete_todo($id);

    // save todos
    $is_saved = Todos::save_todos($todos);
	
    if ($is_saved) {
		return new Response('Todo deleted.', 201);
	}
    else {
        return 'error';
	}
});

// deleting a Todo
/*
$app->post('/todo/delete', function (Request $request) use ($app) {
    $todos = json_decode(file_get_contents('data/todos.json'), true);
    $id = intval($request->request->get('id'));
    //var_dump($todos);
    foreach ($todos as $todo) {
        if ($todo['id'] === $id) {
            $todos = removeElementWithValue($todos, 'id', $id);
            file_put_contents("data/todos.json",json_encode($todos));
            return new Response('Todo deleted.', 201);
        }
    }
    return 'error';
});

// marking Todo as done
$app->post('/todo/done', function (Request $request) use ($app) {
	//$todos = json_decode(file_get_contents('data/todos.json'), true);
	$id = intval($request->request->get('id'));
	$done = strToBool($request->request->get('done'));
	//var_dump($todos);
    $todo = Todos::get_todo($id);
    $fields = array ()
    $todo = Todos::update_todo($id, $fields);
	foreach ($todos as &$todo) {
		if ($todo['id'] === $id) {
			$todo['done'] = $done;
			file_put_contents("data/todos.json",json_encode($todos));
			return new Response('Todo is done. One task less on the list!', 201);
		}
	}
	return 'error';
});
*/
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
            return new Response('Todo is done. One task less on the list!', 201);
        }
    }
    return 'error';
});

// sorting the todos
// deleting a Todo
$app->post('/todo/sort', function (Request $request) use ($app) {
    $todos = json_decode(file_get_contents('data/todos.json'), true);
    $order = $request->request->get('order');
    $newtodos = array();
    var_dump($order);
    // sort $todos according to $order
    foreach ($order as $id) {
        foreach ($todos as $todo) {
            if ($todo['id'] == $id) {
                $newtodos[] = $todo;
                break;
            }
        }
    }
    var_dump($newtodos);
    file_put_contents("data/todos.json",json_encode($newtodos));
    return new Response('Todo resorted.', 201);
});

// Edit an item
$app->get('/todo/edit/{id}', function ($id) use ($app) {
    $todos = json_decode(file_get_contents('data/todos.json'), true);
    foreach ($todos as $todo) {
        if ($todo['id'] == $id) {
            $data = array(
                'Title' => $todo['title'],
                'Body' => $todo['body'],
            );
            break;
        }
    }
    $form = sas_loadForm('Edit', $data, $app);

    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('index2.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// Processing the form
$app->post('/todo/edit/{id}', function (Request $request, $id) use ($app) {
    // WHY CAN I NOT GET THE $id  by passing it in???????
    // Always returns NULL or something else....
    //var_dump($id);
    $referer = $_SERVER["HTTP_REFERER"];
    $pos = strrpos($referer, '/');
    $id = $pos === false ? $$referer : substr($referer, $pos + 1);
    $id = intval($id);
    //var_dump($id);
    $todos = json_decode(file_get_contents('data/todos.json'), true);
    foreach ($todos as $todo) {
        if ($todo['id'] === $id) {
            $data = array(
                'Title' => $todo['title'],
                'Body' => $todo['body'],
            );
            break;
        }
    }
    $form = sas_loadForm('Edit', $data, $app);
    //var_dump($form) . '<br>';
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        //var_dump($data);
        foreach ($todos as &$todo) {
            if ($todo['id'] == $id) {
                $todo = array(
                            'id'    => $todo['id'],
                            'title' => $data['Title'],
                            'body'  => $data['Body'],
                            'done'  => $todo['done'],
                        );
            }
        }
        
        // save todos in a file
        file_put_contents("data/todos.json",json_encode($todos));
        // redirect somewhere
        return $app->redirect('../../twig');
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