<?php

namespace App\Exceptions;

use App\Http\Exceptions\IngredientNotFoundException;
use App\Http\Exceptions\RecipeCreationException;
use App\Http\Exceptions\RecipeNotFoundException;
use App\Http\Exceptions\RecipeUpdateException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    protected $levels = [
        RecipeCreationException::class => 'error',
        RecipeUpdateException::class => 'error'
    ];

    public function register(): void
    {
        $this->renderable(function (RecipeNotFoundException $e, $request) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'RECIPE_NOT_FOUND'
            ], $e->getCode());
        });

        $this->renderable(function (IngredientNotFoundException $e, $request) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'INGREDIENT_NOT_FOUND'
            ], $e->getCode());
        });

        // Handle RecipeCreationException
        $this->renderable(function (RecipeCreationException $e, $request) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'RECIPE_CREATION_FAILED'
            ], $e->getCode());
        });

        // Handle RecipeUpdateException
        $this->renderable(function (RecipeUpdateException $e, $request) {
            return response()->json([
                'error' => $e->getMessage(),
                'code' => 'RECIPE_UPDATE_FAILED'
            ], $e->getCode());
        });

        // Handle ModelNotFoundException
        $this->renderable(function (ModelNotFoundException $e, $request) {
            $modelName = strtolower(class_basename($e->getModel()));
            return response()->json([
                'error' => "The requested {$modelName} was not found",
                'code' => 'MODEL_NOT_FOUND'
            ], 404);
        });

        // Handle ValidationException
        $this->renderable(function (ValidationException $e, $request) {
            return response()->json([
                'error' => 'The given data was invalid',
                'code' => 'VALIDATION_ERROR',
                'details' => $e->errors()
            ], 422);
        });
    }
}
