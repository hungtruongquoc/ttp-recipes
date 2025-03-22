<?php

namespace App\Http\Exceptions;

use Exception, Throwable;
class RecipeCreationException extends Exception
{
    public function __construct(string $reason = "Failed to create recipe", Throwable $previous = null)
    {
        parent::__construct($reason, 500, $previous);
    }
}
