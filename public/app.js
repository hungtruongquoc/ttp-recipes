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

        if (!response.ok) {
            throw new Error(`HTTP error, status ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error(`apiRequest(): ${error}`);
        throw error;
    }
}

function recipesApp() {
    return {
        recipes: [],
        loading: true,
        error: null,
        init() {
            this.fetchRecipes();
        },
        createRecipeComponentFromData (recipeData) {
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
            addIngredient () {
                this.newRecipe.ingredients.push('');
            },
            async submit () {
                const requestBody = {
                    name: this.newRecipe.name,
                    description: this.newRecipe.description,
                    ingredients: this.newRecipe.ingredients
                }

                try {
                    const newRecipe = await apiRequest('api/recipes', 'POST', requestBody);
                    this.recipes.unshift(this.createRecipeComponentFromData(newRecipe));
                    this.error = null;
                } catch (error) {
                    this.error = 'Error adding recipe!';
                }
            },
        },
        startEditingExistingRecipe (recipe) {
            recipe.editingData = JSON.parse(JSON.stringify(recipe.data));
            recipe.editing = true;
        },
        addIngredientToExistingRecipe (recipe) {
            recipe.editingData.ingredients.push({id: null, name: ''});
        },
        async saveExistingRecipe (recipe) {
            try {
                const editedRecipe = JSON.parse(JSON.stringify(recipe.editingData));

                recipe.data = editedRecipe;
                recipe.editing = false;
                recipe.error = null;
            } catch (error) {
                recipe.error = 'Error saving recipe!';
            }
        },
    };
}
