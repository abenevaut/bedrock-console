# Bedrock console

## Installation

**CURRENTLY, THERE IS NO STABLE REVISION**

```
composer require abenevaut/bedrock-console
```

Now you can execute the following command

```
vendor/bin/console
```

### serve command

To use `serve` command, trigger installation process. The following command will create a server.php a your project root.

```
vendor/bin/console serve --install
```

Now you can use PHP built-in server for bedrock

```
vendor/bin/console serve
vendor/bin/console serve localhost
vendor/bin/console serve localhost 9000
vendor/bin/console serve -h
```

## Add commands

- Have a look on [Symfony console](https://symfony.com/doc/4.4/console.html) documentation to create new commands.
    - All commands have to heritate from `src/Command.php` to use the container
- Add composer psr-4 autoloading to load automatically your customs commands
- Edit `config/application.php` then add a global array recording all you customs commands, like follow :

```
$commands = [
    \PSR4\NAMESPACE\MyCustomCommand::class,
];
```
