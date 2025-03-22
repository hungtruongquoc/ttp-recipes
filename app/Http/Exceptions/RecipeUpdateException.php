<?php

namespace App\Http\Exceptions;

use Exception, Throwable;
class RecipeUpdateException extends Exception
{
    public function __construct(string $reason = "Failed to update recipe", Throwable $previous = null)
    {
        parent::__construct($reason, 500, $previous);
    }
}
