# Introduction

We've built a basic recipes app with Laravel 11 that consists of a single-page frontend and an JSON API. Within this codebase, some misguided coding choices have been made, and some backend logic has not been implemented yet. We'd like you to find and fix these mistakes, and add the missing backend code.

To that end - please complete the numbered tasks below. The ones we care about more are closer to the top. These tasks are designed to point you in the right direction to find the hidden issues. Correctly identifying these issues and applying the correct fix is at the heart of this challenge.

Some tasks can be solved with one line, while some require larger code chunks to be added. If something in the existing code doesn't look quite right, please improve it! There isn't necessarily a single right answer for these tasks - feel free to make executive decisions as needed. The goal is for this to feel similar to working in a real project.

We expect this project to take 1-2 hours.

# What we are looking for

Primarily, we are looking for organized, readable code and thoughtfulness in the choices you make while coding. Specifically, we're interested in the following:
- Ability to take initiative when you see something that can be improved
- Simple solutions over those that are complex, abstracted, and extensible
- Consistent code style
- Clear code comments if needed
- Small commits with high-quality commit messages (they don't need to be super detailed, but they should be clear, descriptive, and in the imperative mood)
- Evidence that you have thought about deeper considerations such as edge cases, security, etc.

Please do not use [Laravel resource controllers](https://laravel.com/docs/11.x/controllers#resource-controllers). We prefer to list route methods and controller functions explicitly.

# Getting set up

This application uses Laravel Sail to run a Docker container with everything the application needs. You will need the following installed on your computer:
- [Docker Desktop](https://www.docker.com/products/docker-desktop/) to run the container
- PHP 8.2 and [Composer](https://getcomposer.org/) to initially install the dependencies

More information on setting up Laravel Sail is in the [Laravel docs](https://laravel.com/docs/11.x/installation#docker-installation-using-sail).

If you're using Windows, we recommend running the following commands on [Ubuntu on WSL](https://documentation.ubuntu.com/wsl/en/latest/howto/install-ubuntu-wsl2/).

```sh
# Clone the repository
git clone https://github.com/timetopet/ttp-recipes.git
cd ttp-recipes

# Install dependencies
composer update

# Run the application
./vendor/bin/sail up

# While the application is running, open another terminal and initialize the database
./vendor/bin/sail artisan migrate
./vendor/bin/sail artisan db:seed
```

# File list

These are the current main application files, to save you some time:

```
app/Http/Controllers/RecipeController.php
app/Models/Ingredient.php
app/Models/Recipe.php
public/app.css
public/app.js
resources/views/home.blade.php
routes/api.php
routes/web.php
```

# Tasks

Some of these are written the way we would receive them from a non-technical stakeholder. Keep in mind that there are hidden technical issues behind each of these!

1. If you save a new recipe with the name "Mac & Cheese", the name in the list is just "Mac". This isn't right.

2. When you try to save a new recipe that's blank, it just says "Error adding recipe!". That's not a very helpful error message.

3. When you edit an existing recipe and click Save, it looks like it takes effect, but when you reload the page, those edits are gone.

4. In the `RecipeController` class, the JSON structure that represents a recipe is essentially duplicated. Let's avoid having duplicated code here.

5. The efficiency of the API endpoint that loads recipes on pageload can be improved from a SQL query standpoint.

6. When you save a new recipe, it appears at the top of the list. However, when you reload the page, it's at the bottom. It should still be at the top after reloading.

7. When you save a new recipe, the values are still in the form fields after it's successfully saved - these should be cleared out.

8. If you add a new recipe and double click on the Save button, it adds the recipe twice.

9. There is a frontend vulnerability that would allow a bad actor to run arbitrary code when other people view the recipes. Let's fix this.

# Submitting

Please add your commits, push the repository to your own GitHub account, and share the repository with the following users:

- clabinger
- jeankayy
- joshuahedlund
- pegler

```sh
# Remove the remote repository (you won't be pushing up to our repository)
git remote remove origin

# Create a new repository in your GitHub account, then push up to that repository
git remote add origin <your-repository-url>
git push -u origin main
```
