# Lightning-Fast Blade Components with `@boost`

[![Latest Version on Packagist](https://img.shields.io/packagist/v/angus-mcritchie/blade-boost-directive.svg?style=flat-square)](https://packagist.org/packages/angus-mcritchie/blade-boost-directive)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/angus-mcritchie/blade-boost-directive/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/angus-mcritchie/blade-boost-directive/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/angus-mcritchie/blade-boost-directive/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/angus-mcritchie/blade-boost-directive/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/angus-mcritchie/blade-boost-directive.svg?style=flat-square)](https://packagist.org/packages/angus-mcritchie/blade-boost-directive)

Adds a `@boost` Blade directive to your Laravel application. Caches the HTML rendered from Blade code. Optionally you can perform a simple `str_replace` for any variables passed to the directive.

## Installation

You can install the package via composer:

```bash
composer require angus-mcritchie/blade-boost-directive
```

## Usage
The `@boost` directive can be used to wrap any Blade code. It will render the blade code once and cache the HTML and can optionally replace any variables passed to the directive via the second argument.


### Signature
```blade
@boost(string $name, array $variables = [], string $store = 'array')
    <x-component />
@endboost
```

### Repeating Code Use Case
A common use case would be that you're rendering a page of `<x-post.card />` components which looks like this:

```blade
@foreach($posts as $post)
    <x-card>
        <x-link href="{{ route('post.show', $post) }}">
            <x-image src="{{ $post->image }}" />
        </x-link>
        <x-card.body>
            <x-heading as="h3">{{ $post->title }}</x-heading>
            <x-text>{{ $post->description }}</x-text>
        </x-card.body>
        <x-badge class="absolute top-0 left-0">{{ $post->author }}</x-badge>
        <x-card.footer>
            <x-button href="{{ route('post.show', $post) }}">
                Read More
            </x-button>
        </x-card.footer>
    </x-card>
@endforeach
```

That is a total of 9 components per post, if we have 30 per page, that is 270 components in total and there are only 4 variables for the whole card.

Let's see how we can use the `@boost` directive to speed this up.

```blade
@foreach($posts as $post)
    @boost('post-card', [
        '{name}' => $post->name,
        '{url}' => route('post.show', $post),
        '{image}' => $post->image,
        '{author}' => $post->author,
        '{description}' => $post->description
    ])
        <x-card>
            <x-link href="{url}">
                <x-image src="{image}" />
            </x-link>
            <x-card.body>
                <x-heading as="h3">{title}</x-heading>
                <x-text>{description}</x-text>
            </x-card.body>
            <x-badge class="absolute top-0 left-0">{author}</x-badge>
            <x-card.footer>
                <x-button href="{url}">
                    Read More
                </x-button>
            </x-card.footer>
        </x-card>
    @endboost
@endforeach
```

Wrapping the whole component in the `@boost` directive will store the HTML in the cache, then replace the passed variables with the values from the `$post` object and result in Blade only rendering **a single component instead of 270** 🚀.

You could use the `file` cache store and render zero components (after the first request) but you'll need to clear the cache when you make changes to the component.

### Large Component Use Case
Another common use case would be that you're rendering a page with a single components with many smaller other components.

```blade
<x-footer>
    <x-grid cols="4">
        <x-grid.item>
            <x-link href="{{ route('home') }}">
                <x-image src="{{ asset('images/logo.png') }}" />
            </x-link>
        </x-grid.item>
        <x-grid.item>
        <x-list>
            <x-list.item>
                <x-link href="{{ route('home') }}">
                    Home
                </x-link>
            </x-list.item>
            <x-list.item>
                <x-link href="{{ route('about') }}">
                    About
                </x-link>
            </x-list.item>
            <x-list.item>
                <x-link href="{{ route('contact') }}">
                    Contact
                </x-link>
            </x-list.item>
        </x-list>
        <!-- more columns, links etc -->
        </x-grid.item>
    </x-grid>
    <div class="text-center">
        <x-text>
            &copy; {{ date('Y') }} My Company
        </x-text>
    </div>
</x-footer>
```

To speed this up we can use the `@boost` directive to cache the HTML for the footer and only render it once.
By default, @boost uses the `array` cache store, which is the fastest cache store available but is not persistent, this use case you'll want to use the `file` cache store.
You can do this by passing a third argument to the `@boost` directive.

Let's see how we can use the `@boost` directive to speed this up.
```blade
@boost('footer', ['{year}' => date('Y')], 'file')
    <x-footer>
        <x-grid cols="4">
            <x-grid.item>
                <x-link href="{{ route('home') }}">
                    <x-image src="{{ asset('images/logo.png') }}" />
                </x-link>
            </x-grid.item>
            <x-grid.item>
            <x-list>
                <x-list.item>
                    <x-link href="{{ route('home') }}">
                        Home
                    </x-link>
                </x-list.item>
                <x-list.item>
                    <x-link href="{{ route('about') }}">
                        About
                    </x-link>
                </x-list.item>
                <x-list.item>
                    <x-link href="{{ route('contact') }}">
                        Contact
                    </x-link>
                </x-list.item>
            </x-list>
            <!-- more columns, links etc -->
            </x-grid.item>
        </x-grid>
        <div class="text-center">
            <x-text>
                &copy; {year} My Company
            </x-text>
        </div>
    </x-footer>
@endboost
```

Now, Blade will just render this component once and store the HTML in the cache. The next time the page is loaded, the pre-rendered component will be stored in the `file` cache store. This is useful if you want to speed up large, but simple components that are used on every page and only rendered once.

Mind you that the `@boost` directive will use the `Cache::rememberForever()` under the hood so it's up to you to clear the cache when you make changes to the component.

## Configuration
You can publish the configuration file with the following command:

```bash
php artisan vendor:publish --tag="blade-boost-directive-config"
```
This is the contents of the published config file:

```php
return [

    /**
     * Enable or disable the package.
     * If disabled, the package will not register any Blade directives.
     */
    'enabled' => env('BLADE_BOOST_ENABLED', true),

    /**
     * The default cache store to use.
     * This is used when no cache store is specified in the directive.
     */
    'default_cache_store' => env('BLADE_BOOST_DIRECTIVE_DEFAULT_CACHE_STORE', 'array'),

    /**
     * The prefix to use for cache keys.
     * This is used to avoid key collisions with other packages or parts of your application.
     */
    'prefix' => env('BLADE_BOOST_DIRECTIVE_PREFIX', 'blade-boost-directive.'),
];
```

## Benchmarks
Our [benchmarks](./benchmarks.md) show the performance improvements you can expect when using the `@boost` directive. The benchmarks are not exhaustive and only serve as a demonstration of the performance improvements you can expect.

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

- [Angus McRitchie](https://github.com/angus-mcritchie)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
