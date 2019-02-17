# Laravel Make Full API Resource Files

## About
This Laravel command make:complete-model-set creates all usable Classes
for creating an API.

- Model (with or without Migration)
- Controller with all Requests (ModelIndexRequest, ModelStoreRequest,...)
- Resources (Collection and Single) 

## How To Install

### Composer Setup
`composer require --dev andreaspabst/laravel-make-complete-model-set`

### Register Service Provider
After installing the package via composer, you have to add the Service Provider to your `config/app.php`
```
...
AndreasPabst\LaravelMakeCompleteModelSet\LaravelMakeCompleteModelSetServiceProvider::class,
...
```

## How To Call
`php artisan make:complete-model-set`

It will be a guided wizard through the creating steps.

## Example Wizard

```
$ php artisan make:complete-model-set

Generating complete controller set 

 Enter the Model Name...:
 > Post 

 Do you want a Model? (yes/no) [no]:
 > y

  Crafting Post model...

 Do you want a migration? (yes/no) [no]:
 > y

  Crafting create_posts_table migration...

 Do you want Resources? (yes/no) [no]:
 > y

  Crafting Post resource...
  Crafting PostCollection collection...

 Do you want a controller? (yes/no) [no]:
 > y

  Crafting PostController controller

 Do you want to including all requests into controller creation? (yes/no) [no]:
 > y

  Crafting request...
  Crafting controller...

```

## Other stuff...
Have Fun Using!