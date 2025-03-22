<?php

namespace App\Http\Exceptions;

use Exception;

class IngredientNotFoundException extends Exception
{
    public function __construct($id = null)
    {
        $message = $id ? "Ingredient with ID {$id} not found" : "Ingredient not found";
        parent::__construct($message, 404);
    }
}
