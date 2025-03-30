<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\HtmlString;

beforeEach(function () {
    Artisan::call('view:clear');
});

it('can repeat without variables', function () {
    expectBlade(<<<'BLADE'
        @repeated('foo')
            foo
        @endrepeated
    BLADE)
        ->toContain('foo');
});

it('can repeat with variables', function () {
    expectBlade(<<<'BLADE'
        @foreach(['Angus', 'John'] as $name)
            @repeated('hello', ['{name}' => $name])
                Hello, {name}!
            @endrepeated
        @endforeach
    BLADE)
        ->toContain('Hello, John!')
        ->toContain('Hello, Angus!');
});

it('can throw exception when no name provided', function () {
    expectBlade(<<<'BLADE'
        @repeated
            foo
        @endrepeated
    BLADE);
})->throws(InvalidArgumentException::class);

it('can nest named repeat', function () {
    expectBlade(<<<'BLADE'
        @repeated('level-1')
            level-1

            @repeated('level-2')
                level-2
            @endrepeated
        @endrepeated
    BLADE)
        ->toContain('level-1')
        ->toContain('level-2');
});

it('can nest anonymous repeat', function () {
    expectBlade(<<<'BLADE'
        @repeated('level-1-and-2')
            level-1

            @repeated('level-2')
                level-2
            @endrepeated
        @endrepeated
    BLADE)
        ->toContain('level-1')
        ->toContain('level-2');
});

it('can change cache store', function () {
    expectBlade(<<<'BLADE'
        @repeated('foo', ['{var}' => 'bar'], 'file')
            foo: {var}
        @endrepeated
    BLADE)
        ->toContain('foo: bar');

    expect(cache()->store('file')->get('foo'))
        ->toBeInstanceOf(HtmlString::class);

    expect(cache()->store('file')->get('foo')->toHtml())
        ->toContain('foo: {var}');
});
