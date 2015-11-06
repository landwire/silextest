<?php

class Todos {

	function get_todos() {
		$todos = json_decode(file_get_contents('data/todos.json'), true);
		if ($todos) {
			return $todos;
		}
		else {
			return false;
		}
	}

	function save_todos($todos) {
		$file = file_put_contents("data/todos.json",json_encode($todos));
		if ($file) {
			return true;
		}
		else {
			return false;
		}
	}

	function create_todo($data) {
		$todos = self::get_todos();
		// determine unique id
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

        // create the new todo
        $todos[] = array(
        				'id'	=> $new_id,
        				'title' => $data['title'],
        				'body' 	=> $data['body'],
        				'done' 	=> false,
        			);

        // return updated todos
        return $todos;
	}

	function get_todo($id) {
		$todos = self::get_todos();
		foreach ($todos as &$todo) {
			if ($todo['id'] === $id) {
				return $todo;
			}
		}
		return false;
	}

	function update_todo($id, $data) {
		$todos = self::get_todos();

		foreach ($todos as &$todo) {
	        if ($todo['id'] === $id) {
	        	$todo = array_replace($todo, $data);
	        	break;
	        }
	    }

	    return self::save_todos($todos);
	}

	function delete_todo($id) {
		$todos = self::get_todos();
		$todos = removeElementWithValue($todos, 'id', $id);
		
		return self::save_todos($todos);
	}

	function sort_todos($order) {
		$todos = self::get_todos();
		//$order = $request->request->get('order');
		$newtodos = array();
		//var_dump($order);
		// sort $todos according to $order
		foreach ($order as $id) {
		    foreach ($todos as $todo) {
		        if ($todo['id'] == $id) {
		            $newtodos[] = $todo;
		            break;
		        }
		    }
		}
		
		return self::save_todos($newtodos);
		
	}
}