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
        				'title' => $data['Title'],
        				'body' 	=> $data['Body'],
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

	function update_todo($id, $props) {
		$todo = self::get_todo($id);
		foreach ($props as $prop) {

			if ($todo['id'] === $id) {
				$todo['done'] = $done;
				file_put_contents("data/todos.json",json_encode($todos));
				return new Response('Todo is done. One task less on the list!', 201);
			}
		}
	}

	function delete_todo($id) {
		$todos = self::get_todos();
		$todos = removeElementWithValue($todos, 'id', $id);
		return $todos;
	}

}