<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('view:clear');
});

it('can repeat a block of code', function () {
    expectBlade(<<<'blade'
        @repeated('hello-world')
            Hello World!
        @endrepeated
    blade)
        ->toContain('Hello World!');
});

it('can repeat a block of code with zero arguments', function () {
    expectBlade(<<<'blade'
        @repeated
            Hello World!
        @endrepeated
    blade)
        ->toContain('Hello World!');
});

it('can repeat a block of code with variables', function () {
    expectBlade(<<<'blade'
        @foreach(['Angus', 'John'] as $name)
            @repeated('hello', ['{name}' => $name])
                Hello, {name}!
            @endrepeated
        @endforeach
    blade)
        ->toContain('Hello, John!')
        ->toContain('Hello, Angus!');
});

it('can repeat a block of code with a trailing comma', function () {
    expectBlade(<<<'blade'
        @repeated
            Hello World!
        @endrepeated
    blade)
        ->toContain('Hello World!');

    expectBlade(<<<'blade'
        @repeated('hello', ['{name}' => 'Angus'],)
            Hello {name}!
        @endrepeated
    blade)
        ->toContain('Hello Angus!');
});
