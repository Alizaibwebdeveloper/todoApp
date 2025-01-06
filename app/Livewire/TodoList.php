<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Todo;

class TodoList extends Component
{
    public $name;
    public $search;

    public $todoId;
    public $newName;

    
    public function addTodo()
    {
       $validated= $this->validate([
            'name' => 'required'
        ]);

        $todos = new Todo();
        $todos::create($validated);

        $this->reset('name');

        session()->flash('message', 'Todo added successfully.');


    }

    public function edit($todoId)
    {
        // Set the current todoId for editing
        $this->todoId = $todoId;
    
        // Find the Todo item by ID
        $todo = Todo::find($todoId);
    
        // Check if the Todo item exists
        if ($todo) {
            // Set the current name of the todo for editing
            $this->newName = $todo->name;
        } else {
            // Flash an error message if the Todo item is not found
            session()->flash('error', 'Todo not found.');
        }
    }
    
    public function updateTodo()
    {
        // Validate the input to ensure 'newName' is provided
        $validated = $this->validate([
            'newName' => 'required'
        ]);

        // Find the Todo item by ID
        $todo = Todo::find($this->todoId);
    
        // Check if the Todo item exists
        if ($todo) {
            // Update the name field with the validated new name
            $todo->name = $validated['newName'];
            
            // Save the updated Todo item
            $todo->save();
    
            // Reset the editing state
            $this->todoId = null;
            $this->newName = '';
    
            // Flash a success message to the session
            session()->flash('message', 'Todo updated successfully.');
        } else {
            // Flash an error message if the Todo item is not found
            session()->flash('error', 'Todo not found.');
        }
    }

    public function cancelEdit()
    {
        $this->todoId = null;
        $this->newName = '';
        
    }

    

    public function deleteTodo($id)
    {
        $todos = Todo::find($id);
        $todos->delete();

        session()->flash('message', 'Todo deleted successfully.');
    }

    public function toggleTodoStatus($id)
    {
        $todos = Todo::find($id);
        $todos->completed = !$todos->completed;
        $todos->save();

        session()->flash('message', 'Todo updated successfully.');
    }
    
    public function render()
    {
        $todos = Todo::latest()->where('name', 'like', '%'.$this->search.'%')->paginate(5);
        
        return view('livewire.todo-list', [
            'todos' => $todos
        ]);
    }
    }

