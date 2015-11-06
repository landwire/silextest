<?php
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Provider\FormServiceProvider;
use Symfony\Component\Validator\Constraints as Assert;

// routers and what they do
// would be great to separate the two......

// Todo List & Form
$app->get('/todo', function () use ($app) {
    
    // create the form fields
    $data = array(
                'title' => 'What do you need to todo?',
                'body' => 'Tip: Buying flowers for your sweetheart is always a nice thing to do!',
            );
    $form = sas_loadForm('Todo', $data, $app);
    
    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    // get todos to pass to the app fro rendering
    $todos = Todos::get_todos();

    // render the app
    return $app['twig']->render('base.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// Processing the form
$app->post('/todo', function (Request $request) use ($app) {
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
            return $app->redirect('/todo');
        }
        else {
            return 'error';
        }
    }
   
    // form must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    return $app['twig']->render('base.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// deleting a Todo
$app->post('/todo/delete', function (Request $request) use ($app) {
    // get variables from $request
	$id = intval($request->request->get('id'));
	
    // delete todo returns new array of todos
    $is_deleted = Todos::delete_todo($id);

    if ($is_deleted) {
		return new Response('Todo deleted.', 201);
	}
    else {
        return 'error';
	}
});

// mark as done
$app->post('/todo/done', function (Request $request) use ($app) {
    $id = intval($request->request->get('id'));
    $done = strToBool($request->request->get('done'));

    // set up array with new data from form
    $data = array(
        'done' => $done,
    );

    $is_updated = Todos::update_todo($id, $data);
    
    if ($is_updated) {
        return new Response('Todo updated.', 201);
    }
    else {
        return 'error';
    }
});


// sorting the todos
$app->post('/todo/sort', function (Request $request) use ($app) {
    $order = $request->request->get('order');

    $is_sorted = Todos::sort_todos($order);
    
    if ($is_sorted) {
        return new Response('Todo sorted.', 201);
    }
    else {
        return 'error';
    }
});

// Edit an item
$app->get('/todo/edit/{id}', function ($id) use ($app) {
    $id = intval($id);

    // load the edit form with existing data
    $data = Todos::get_todo($id);
    $form = sas_loadForm('Edit', $data, $app);

    // from must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    // get todos to pass to the app fro rendering
    $todos = Todos::get_todos();

    return $app['twig']->render('edit.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});

// Processing the edit form
$app->post('/todo/edit/{id}', function (Request $request, $id) use ($app) {
    // WHY CAN I NOT GET THE $id  by passing it in???????
    // Always returns NULL or something else....
    
    //pull id from referer instead
    $referer = $_SERVER["HTTP_REFERER"];
    $pos = strrpos($referer, '/');
    $id = $pos === false ? $$referer : substr($referer, $pos + 1);
    $id = intval($id);

    // load the edit form with existing data
    $data = Todos::get_todo($id);
    $form = sas_loadForm('Edit', $data, $app);
    
    $form->handleRequest($request);

    if ($form->isValid()) {
        $data = $form->getData();
        // set up array with new data from form

        $is_updated = Todos::update_todo($id, $data);
        
        if ($is_updated) {
            // redirect somewhere
            return $app->redirect('/todo');
        }
        else {
            return 'error';
        }
    }
   
    // form must be created after submission handlers and validation!!!!
    if ($form) {
        $form = $form->createView();
    }

    // get todos to pass to the app fro rendering
    $todos = Todos::get_todos();

    return $app['twig']->render('edit.twig', array(
            'form' => $form,
            'todos'=> $todos
    ));
});