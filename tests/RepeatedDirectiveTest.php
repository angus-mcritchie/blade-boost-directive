<?php

use Illuminate\Support\Facades\Artisan;

beforeEach(function () {
    Artisan::call('view:clear');
});

it('can repeat without variables', function () {
    expectBlade(<<<'blade'
        @repeated('hello-world')
            Hello World!
        @endrepeated
    blade)
        ->toContain('Hello World!');
});

it('can repeat with variables', function () {
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
