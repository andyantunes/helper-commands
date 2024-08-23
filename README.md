# User Activities Log

Helper Class Generator to generate the user's log activities

## Installation

Install the package via composer

```bash
composer require andyantunes/helper-commands --dev
```

Publish the `migration` and the `config/activities.php` config file

```bash
php artisan vendor:publish --provider="AndyAntunes\\UserActivities\\UserActivitiesServiceProvider"
```

Before running migration

* Check the configuration file `config/activities.php` and change the configurations if you need

Run the migration

```bash
php artisan migrate
```

## Using

To generate the classes and methods use this command

```bash
php artisan make:activity
```

and select the Model which you use to generate the Observer.

## The Observer

The observer is generated with the following methods

```php
public function created(MyModel $myModel): void
{
    $action = "Criou o myModel de ID: {$myModel->id}";
    RecentActivity::setAction($action)
        ->create();
}
```

```php
public function updated(MyModel $myModel): void
{
    $action = "Atualizou o myModel de ID: {$myModel->id}";
    RecentActivity::setAction($action)
        ->create();
}
```

```php
public function deleted(MyModel $myModel): void
{
    $action = "Deletou o myModel de ID: {$myModel->id}";
    RecentActivity::setAction($action)
        ->create();
}
```

```php
public function restored(MyModel $myModel): void
{
    $action = "Restaurou o myModel de ID: {$myModel->id}";
    RecentActivity::setAction($action)
        ->create();
}
```

```php
public function forceDeleted(MyModel $myModel): void
{
    $action = "Removeu o myModel de ID: {$myModel->id}";
    RecentActivity::setAction($action)
        ->create();
}
```

You can edit the activity action as you wish.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
