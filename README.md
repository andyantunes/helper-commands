# Helper Commands

Commands to help in the development of laravel projects

## Installation

Install the package via composer

```bash
composer require andyantunes/helper-commands --dev
```

Publish the `migration` to use the `Log Activities` and the `config/helper-commands.php` config file

```bash
php artisan vendor:publish --provider="AndyAntunes\\HelperCommands\\HelperCommandsServiceProvider"
```

Before running migration

* Check the configuration file `config/helper-commands.php` and change the configurations if you need

Run the migration

```bash
php artisan migrate
```

## Using the Activity Log

To generate the classes and methods use this command

```bash
php artisan helper:activity
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

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
