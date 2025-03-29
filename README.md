# Lightning-Fast Blade Components with `@repeated`

[![Latest Version on Packagist](https://img.shields.io/packagist/v/angus-mcritchie/blade-repeated-directive.svg?style=flat-square)](https://packagist.org/packages/angus-mcritchie/blade-repeated-directive)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/angus-mcritchie/blade-repeated-directive/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/angus-mcritchie/blade-repeated-directive/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/angus-mcritchie/blade-repeated-directive/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/angus-mcritchie/blade-repeated-directive/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/angus-mcritchie/blade-repeated-directive.svg?style=flat-square)](https://packagist.org/packages/angus-mcritchie/blade-repeated-directive)

Adds a `@repeated` Blade directive to your Laravel application. Only speed up performance if you're rendering the same component multiple times as it renderes the component once and caches the output. Then does a simple `str_replace` for any variables passed to the directive (optional).

## Installation

You can install the package via composer:

```bash
composer require angus-mcritchie/blade-repeated-directive
```

## Usage
The `@repeated` directive can be used to wrap any Blade component. It will render the component once and cache the output. Then it will replace any variables passed to the directive.
This is very useful if you're rendering many of the same components in a loop or if you're rendering the same component multiple times in a view.

### With Variables
You can pass variables to the `@repeated` directive. The variables will be replaced in the output of the component.

```blade
@foreach($posts as $post)
    @repeated('post-card', ['name' => $post->name, 'href' => route('post.show', $post)])
        <x-post.card name="{name}" href="{href}" />
    @endrepeated
@endforeach
```

### Without Variables
You can still get excellent performace improvments for components that don't use any variables.

```blade
@foreach($posts as $post)
    @repeated('post-card-BladeRepeatedDirective')
        <x-post.card-BladeRepeatedDirective />
    @endrepeated
@endforeach
```

## Benchmarks
Our [benchmarks](./benchmarks.md) show the performance improvements you can expect when using the `@repeated` directive. The benchmarks are not exhaustive and only serve as a demonstration of the performance improvements you can expect.

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [:author_name](https://github.com/:author_username)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
