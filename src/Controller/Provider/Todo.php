<?php
// src/Controller/Provider/Todo.php
namespace Sascha;

use Silex\Application;
use Silex\ControllerProviderInterface;

class Todo implements ControllerProviderInterface{

    public function connect(Application $app)
    {
        $todos = $app["controllers_factory"];

        $todos->get("/", "Controller\\UserController::index");

        $todos->post("/", "Controller\\UserController::store");

        $todos->get("/{id}", "Controller\\UserController::show");

        $todos->get("/edit/{id}", "Controller\\UserController::edit");

        $todos->put("/{id}", "Controller\\UserController::update");

        $todos->delete("/{id}", "Controller\\UserController::destroy");

        return $todos;
    }

}