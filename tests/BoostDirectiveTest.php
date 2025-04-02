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
            bar
        @endboost
    BLADE)
        ->toContain('bar');
});

it('can update default cache store', function () {

    $key = Boost::prefix('foo');
    $cache = Cache::store('file');

    config()->set('blade-boost-directive.default_cache_store', 'file');

    expectBlade(<<<'BLADE'
        @boost('foo')
            bar
        @endboost
    BLADE)
        ->toContain('bar');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('bar');
});

it('can repeat with variables', function () {
    expectBlade(<<<'BLADE'
        @foreach(['foo', 'bar'] as $value)
            @boost('foo', ['replace' => ['{value}' => $value]])
                key: {value}
            @endboost
        @endforeach
    BLADE)
        ->toContain('key: foo')
        ->toContain('key: bar');
});

it('can replace with escape', function () {
    expectBlade(<<<'BLADE'
        @boost('foo', ['replace' => ['{value}' => html_entity_decode('&lt;script&gt;alert(1)&lt;/script&gt;')]])
            key: {value}
        @endboost
    BLADE)
        ->toContain('key: &lt;script&gt;alert(1)&lt;/script&gt;');
});

it('can replace with raw html', function () {
    expectBlade(<<<'BLADE'
        @boost('foo', ['raw' => true, 'replace' => ['{value}' => html_entity_decode('&lt;script&gt;alert(1)&lt;/script&gt;')]])
            key: {value}
        @endboost
    BLADE)
        ->toContain('key: '.html_entity_decode('&lt;script&gt;alert(1)&lt;/script&gt;'));
});

it('can throw exception when no arguments provided', function () {
    expectBlade(<<<'BLADE'
        @boost
            foo
        @endboost
    BLADE);
})->throws(Exception::class, '@boost directive requires `key` to be defined');

it('can throw exception when missing key argument', function () {
    expectBlade(<<<'BLADE'
        @boost(['replace' => ['{key}' => 'value']])
            foo: {key}
        @endboost
    BLADE);
})->throws(Exception::class, '@boost directive requires `key` to be defined');

it('can nest boost', function () {
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

it('can change cache store', function () {

    $key = Boost::prefix('foo');
    $cache = Cache::store('file');

    expectBlade(<<<'BLADE'
        @boost('foo', ['store' => 'file'])
            foo
        @endboost
    BLADE)
        ->toContain('foo');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo');
});

it('can clear cache', function () {

    $key = Boost::prefix('foo');
    $cache = Cache::store('array');

    expectBlade(<<<'BLADE'
        @boost('foo', ['replace' => ['{value}' => 'bar']])
            foo: {value}
        @endboost
    BLADE)
        ->toContain('foo: bar');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo: {value}');

    $cache->forget($key);

    expect($cache->get($key))
        ->toBeNull();

    expectBlade(<<<'BLADE'
        @boost('foo', ['replace' => ['{value}' => 'bar']])
            foo, but different: {value}
        @endboost
    BLADE)
        ->toContain('foo, but different: bar');

    expect($cache->get($key))
        ->toBeInstanceOf(HtmlString::class);

    expect($cache->get($key)->toHtml())
        ->toContain('foo, but different: {value}');
});

it('can execute readme examples', function () {
    expectBlade(<<<'BLADE'
        @foreach(['Post 1', 'Post 2'] as $title)
            @boost([
                'key' => 'post-card',
                'replace' => [
                    '{title}' => $title
                ]
            ])
                <div>{title}<div>
            @endboost
        @endforeach
    BLADE)
        ->toContain('Post 1')
        ->toContain('Post 2');
});
