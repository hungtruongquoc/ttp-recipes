async function apiRequest(path, method = 'GET', body = null) {
    const options = {
        method,
        headers: {
            'Accept': 'application/json',
        }
    };

    if (body) {
        options.headers['Content-Type'] = 'application/json';
        options.body = JSON.stringify(body);
    }

    try {
        const response = await fetch(path, options);

        // Parse JSON response
        const data = await response.json();

        if (!response.ok) {
            const error = new Error(`HTTP error, status ${response.status}`);
            error.response = response;
            error.data = data;
            throw error;
        }

        return data;
    } catch (error) {
        console.error(`apiRequest(): ${error}`);
        throw error;
    }
}

function resetValidationErrors(target) {
    target.validationErrors = {};
}

function validateRecipe(recipe) {
    const errors = {};

    // Validate name
    if (!recipe.name || recipe.name.trim() === '') {
        errors.name = 'Recipe name is required.';
    } else if (recipe.name.length > 255) {
        errors.name = 'Recipe name cannot exceed 255 characters.';
    }

    // Validate description
    if (!recipe.description || recipe.description.trim() === '') {
        errors.description = 'Recipe description is required.';
    }

    // Validate ingredients
    if (!recipe.ingredients || !Array.isArray(recipe.ingredients) || recipe.ingredients.length === 0) {
        errors.ingredients = 'At least one ingredient is required.';
    } else {
        // Check if any ingredients are empty
        const emptyIngredients = recipe.ingredients.some(ingredient => {
            // Handle both new recipe (string) and editing (object with name property)
            const ingredientName = typeof ingredient === 'string' ? ingredient : ingredient.name;
            return !ingredientName || ingredientName.trim() === '';
        });

        if (emptyIngredients) {
            errors.ingredients = 'All ingredients must have a name.';
        }
    }

    return {
        isValid: Object.keys(errors).length === 0,
        errors
    };
}

function useRecipeSubmit() {
    async function submitRecipe(apiPath, method, data) {
        try {
            const result = await apiRequest(apiPath, method, data);
            return {success: true, data: result, error: null};
        } catch (error) {
            // Handle validation errors (422)
            if (error.response && error.response.status === 422) {
                return {
                    success: false,
                    data: null,
                    error: processValidationErrors(error)
                };
            } else {
                return {
                    success: false,
                    data: null,
                    error: 'Error saving recipe!'
                };
            }
        }
    }

    return {submitRecipe};
}

// Helper function to process validation errors
function processValidationErrors(error) {
    if (error.data && error.data.errors) {
        const errorMessages = [];
        for (const field in error.data.errors) {
            errorMessages.push(error.data.errors[field][0]);
        }
        return errorMessages.join('\n');
    }
    return 'An error occurred while processing your request.';
}

function recipesApp() {
    const {submitRecipe} = useRecipeSubmit();
    return {
        recipes: [],
        loading: true,
        error: null,
        validationErrors: {},
        init() {
            this.fetchRecipes();
        },
        toggleNewRecipeForm() {
            this.newRecipe.show = !this.newRecipe.show;
        },
        cancelEditing(recipe) {
            recipe.editing = false;
            recipe.error = null;
            resetValidationErrors(recipe)
        },
        createRecipeComponentFromData(recipeData) {
            return {
                data: recipeData,
                editingData: {},
                editing: false,
                error: null,
            };
        },
        async fetchRecipes() {
            try {
                this.recipes = (await apiRequest('/api/recipes')).map(this.createRecipeComponentFromData);
                this.error = null;
            } catch (error) {
                this.error = 'Error fetching recipes!';
            } finally {
                this.loading = false;
            }
        },
        newRecipe: {
            name: '',
            description: '',
            ingredients: [],
            show: false,
            validationErrors: {},
            submitting: false,
            validate() {
                resetValidationErrors(this);
                const {name, description, ingredients} = this;
                const result = validateRecipe({
                    name,
                    description,
                    ingredients
                });

                this.validationErrors = result.errors;
                return result.isValid;
            },
            addIngredient() {
                this.newRecipe.ingredients.push('');
            },
            async submit() {
                // Validate before submission
                if (!this.newRecipe.validate()) {
                    return; // Stop if validation fails
                }

                const {name, description, ingredients} = this.newRecipe;

                const requestBody = {name, description, ingredients};
                this.newRecipe.submitting = true;

                try {
                    const {success, data, error} = await submitRecipe(
                        'api/recipes',
                        'POST',
                        requestBody
                    );

                    if (success) {
                        this.recipes.unshift(this.createRecipeComponentFromData(data));
                        this.error = null;

                        // Clear form after successful submission
                        this.newRecipe.name = '';
                        this.newRecipe.description = '';
                        this.newRecipe.ingredients = [];
                        this.newRecipe.show = false;
                    } else {
                        this.error = error;
                    }
                } catch (e) {
                    this.error = 'Unexpected error during submission';
                }
                finally {
                    this.newRecipe.submitting = false;
                }
            },
        },
        startEditingExistingRecipe(recipe) {
            recipe.editingData = JSON.parse(JSON.stringify(recipe.data));
            recipe.editing = true;
        },
        addIngredientToExistingRecipe(recipe) {
            recipe.editingData.ingredients.push({id: null, name: ''});
        },
        validateExistingRecipe(recipe) {
            resetValidationErrors(recipe);
            const {name, description, ingredients} = recipe.editingData;
            const result = validateRecipe({
                name,
                description,
                ingredients
            });

            recipe.validationErrors = result.errors;
            return result.isValid;
        },
        async saveExistingRecipe(recipe) {
            const {ingredients} = recipe.editingData;
            // Filter out ingredients with empty names before creating the copy
            if (ingredients && Array.isArray(ingredients)) {
                recipe.editingData.ingredients = ingredients.filter(
                    ingredient => ingredient.name && ingredient.name.trim() !== ''
                );
            }

            // Validate before submission
            if (!this.validateExistingRecipe(recipe)) {
                return; // Stop if validation fails
            }

            const editedRecipe = JSON.parse(JSON.stringify(recipe.editingData));

            const {success, data, error} = await submitRecipe(
                `/api/recipes/${recipe.data.id}`,
                'PUT',
                editedRecipe
            );

            if (success) {
                recipe.data = data;
                recipe.editing = false;
                recipe.error = null;
            } else {
                recipe.error = error;
            }
        },
    };
}
