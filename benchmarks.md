# Benchmarks
Take these benchmarks with a grain of salt. They are not exhaustive and only serve as a demonstration of the performance improvements you can expect when using the `@boost` directive.

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
        'boost' => fn () => Blade::render(
            <<<'BLADE'
                @foreach (collect()->range(1, 100) as $iteration)
                    @boost('new-badge')
                        <flux:tooltip content="This post was added in the last 24 hours">
                            <flux:badge variant="success" size="sm">New</flux:badge>
                        </flux:tooltip>
                    @endboost('new-badge')
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
| boost     | 0.737ms   |

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
        'boost' => fn () => Blade::render(
            <<<'BLADE'
                @foreach (collect()->range(1, 500) as $iteration)
                    @boost('new-badge')
                        <flux:badge>My Button</flux:badge>
                    @endboost('new-badge')
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
| boost     | 2.792ms   |
