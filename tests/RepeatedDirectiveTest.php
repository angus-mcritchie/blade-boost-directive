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


it('can nest named repeat', function () {
    expectBlade(<<<'blade'
        @repeated('level-1')
            level 1

            @repeated('level-2')
                level 2
            @endrepeated
        @endrepeated
    blade)
        ->toContain('Hello World!')
        ->toContain('Hello World 2!')
    ;
});

it('can nest anonymous repeat', function () {
    expectBlade(<<<'blade'
        @repeated
            level 1

            @repeated
                level 2
            @endrepeated
        @endrepeated
    blade)
        ->toContain('level 1')
        ->toContain('level 2')
    ;
});
