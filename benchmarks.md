# Benchmarks
Take these benchmarks with a grain of salt. They are not exhaustive and only serve as a demonstration of the performance improvements you can expect when using the `@repeated` directive.

### 100x Flux UI Badges w/ Tooltips
#### Code
```php
Benchmark::dd(
    benchmarkables: [
        'blade' => fn () => Blade::render(
            <<<'BLADE'
                @foreach (collect()->range(1, 100) as $iteration)
                    <flux:tooltip content="This post was added in the last 24 hours">
                        <flux:badge variant="success" size="sm">New</flux:badge>
                    </flux:tooltip>
                @endforeach
            BLADE
        ),
        'repeated' => fn () => Blade::render(
            <<<'BLADE'
                @foreach (collect()->range(1, 100) as $iteration)
                    @repeated('new-badge')
                        <flux:tooltip content="This post was added in the last 24 hours">
                            <flux:badge variant="success" size="sm">New</flux:badge>
                        </flux:tooltip>
                    @endrepeated('new-badge')
                @endforeach
            BLADE
        ),
    ],
    iterations: 100
);
```

#### Results (21x faster)
| Benchmark | Time (ms) |
| --------- | --------- |
| blade     | 15.834ms  |
| repeated  | 0.737ms   |

### 500x Flux UI Buttons
#### Code
```php
Benchmark::dd(
    benchmarkables: [
        'blade' => fn () => Blade::render(
            <<<'BLADE'
                @foreach (collect()->range(1, 500) as $iteration)
                    <flux:badge>My Button</flux:badge>
                @endforeach
            BLADE
        ),
        'repeated' => fn () => Blade::render(
            <<<'BLADE'
                @foreach (collect()->range(1, 500) as $iteration)
                    @repeated('new-badge')
                        <flux:badge>My Button</flux:badge>
                    @endrepeated('new-badge')
                @endforeach
            BLADE
        ),
    ],
    iterations: 100
);
```

#### Results (14x faster)
| Benchmark | Time (ms) |
| --------- | --------- |
| blade     | 39.834ms  |
| repeated  | 2.792ms   |
