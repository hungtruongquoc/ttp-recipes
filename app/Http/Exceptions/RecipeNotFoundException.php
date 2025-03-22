<?php

namespace App\Http\Exceptions;

use Exception;
class RecipeNotFoundException extends Exception
{
    public function __construct($id = null)
    {
        $message = $id ? "Recipe with ID {$id} not found" : "Recipe not found";
        parent::__construct($message, 404);
    }
}
