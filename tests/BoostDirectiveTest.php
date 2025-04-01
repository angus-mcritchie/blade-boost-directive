<?php

use AngusMcritchie\BladeBoostDirective\Boost;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\HtmlString;

beforeEach(function () {
    Artisan::call('view:clear');
    Cache::store('array')->flush();
    Cache::store('file')->flush();
});

it('can repeat without variables', function () {
    expectBlade(<<<'BLADE'
        @boost('foo')
            foo
        @endboost
    BLADE)
        ->toContain('foo');
});

it('can update default cache store', function () {

    $key = Boost::prefix('foo');
    $cache = Cache::store('file');

    config()->set('blade-boost-directive.default_cache_store', 'file');

    expectBlade(<<<'BLADE'
        @boost('foo')
            foo
        @endboost
    BLADE)
        ->toContain('foo');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo');
});

it('can repeat with variables', function () {
    expectBlade(<<<'BLADE'
        @foreach(['Angus', 'John'] as $name)
            @boost('hello', ['{name}' => $name])
                Hello, {name}!
            @endboost
        @endforeach
    BLADE)
        ->toContain('Hello, John!')
        ->toContain('Hello, Angus!');
});

it('can repeat with variables with nested array arguments and 3rd parameter', function () {
    expectBlade(<<<'BLADE'
        @foreach(['Angus', 'John'] as $name)
            @boost('hello', ['{name}' => $name, '{greeting}' => 'Hello'], 'file')
                {greeting}, {name}!
            @endboost
        @endforeach
    BLADE)
        ->toContain('Hello, John!')
        ->toContain('Hello, Angus!');
});

it('can throw exception when no name provided', function () {
    expectBlade(<<<'BLADE'
        @boost
            foo
        @endboost
    BLADE);
})->throws(Illuminate\View\ViewException::class, 'The name of the cache key is required.');

it('can nest named repeat', function () {
    expectBlade(<<<'BLADE'
        @boost('level-1')
            level-1

            @boost('level-2')
                level-2
            @endboost
        @endboost
    BLADE)
        ->toContain('level-1')
        ->toContain('level-2');
});

it('can nest anonymous repeat', function () {
    expectBlade(<<<'BLADE'
        @boost('level-1-and-2')
            level-1

            @boost('level-2')
                level-2
            @endboost
        @endboost
    BLADE)
        ->toContain('level-1')
        ->toContain('level-2');
});

it('can change cache store', function () {

    $key = Boost::prefix('foo');
    $cache = Cache::store('file');

    expectBlade(<<<'BLADE'
        @boost('foo', ['{var}' => 'bar'], 'file')
            foo: {var}
        @endboost
    BLADE)
        ->toContain('foo: bar');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo: {var}');
});

it('can clear cache', function () {

    $key = Boost::prefix('foo');
    $cache = Cache::store('array');

    expectBlade(<<<'BLADE'
        @boost('foo', ['{var}' => 'bar'], 'array')
            foo: {var}
        @endboost
    BLADE)
        ->toContain('foo: bar');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo: {var}');

    $cache->forget($key);

    expect($cache->get($key))
        ->toBeNull();

    expectBlade(<<<'BLADE'
        @boost('foo', ['{var}' => 'bar'], 'array')
            foo, but different: {var}
        @endboost
    BLADE)
        ->toContain('foo, but different: bar');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo, but different: {var}');
});
