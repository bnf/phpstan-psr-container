# PHPStan dynamic return type extension for PSR-11 ContainerInterface

## Installation

To use this extension, require
[bnf/phpstan-psr-container](https://packagist.org/packages/bnf/phpstan-psr-container)
in [Composer](https://getcomposer.org/):

```
composer require --dev bnf/phpstan-psr-container
```

Include extension.neon in your project's PHPStan config or use [`phpstan/extension-installer`](https://github.com/phpstan/extension-installer):

```
includes:
    - vendor/bnf/phpstan-psr-container/extension.neon
```
