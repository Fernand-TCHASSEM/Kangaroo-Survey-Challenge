# Kangaroo Survey Challenge — Backend

Laravel API that aggregates in-store survey answers stored as JSON files.
Surveys are grouped by a `code` (one code per store); each question is
aggregated across every file sharing that code, using a strategy class picked
per question type.

## Requirements

- PHP 8.4+ with the extensions Laravel needs (mbstring, zip, intl, pdo, ...)
- Composer 2

## Running the backend

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

The API is then available at `http://localhost:8000/api/...`.

A `Dockerfile` (Apache + PHP 8.4) is also included for containerized setups;
mount the project into `/var/www/html` and run `composer install` inside the
container the first time.

## Where the data lives

The 15 survey JSON files live at `storage/app/data/` (`1.json` ... `15.json`)
and are committed to the repo — they're the persistence layer for this app,
not disposable runtime data.

They're read through a dedicated `surveys` filesystem disk
(`config/filesystems.php`, rooted at `storage_path('app/data')`) rather than
the default `local` disk, whose root is `storage/app/private` as of
Laravel 11+. The disk name itself is bound via `config/surveys.php`
(`config('surveys.disk')`), so tests can swap in a fake/fixtures disk without
touching the repository class.

`App\Repositories\SurveyRepository` is the only class that knows about this
disk. It loads and decodes the JSON files and groups them by `code`;
everything above it depends on the repository, never on the filesystem
directly.

## API endpoints

- `GET /api/list.json` → `[{ "code": "XX1", "name": "Paris" }, ...]`
- `GET /api/{code}.json` → `[{ "type", "label", "result" }, ...]` aggregated
  results for that code, or `404` for an unknown code.

## Design notes: aggregation via the Strategy pattern

The core requirement is that adding a new question type never requires
touching the controller. This is done with one class per question type plus a
registry that picks the right one:

```
app/Aggregation/
  AggregatorInterface.php   # supports(string $type): bool; aggregate(array $answers, ?array $options): mixed
  QcmAggregator.php         # counts how many files answered `true` per option
  NumericAggregator.php     # averages the numeric answers
  DateAggregator.php        # bonus: proves a new type needs no other changes
  AggregatorRegistry.php    # returns the aggregator whose supports() matches
```

`App\Services\SurveyAggregationService` orchestrates one code: it asks the
repository for the surveys sharing that code, then for each question asks the
registry for the right aggregator and calls `aggregate()` on the answers
collected across all files. `App\Http\Controllers\SurveyController` only
calls the service and returns JSON — it never branches on question type.

The aggregator list is bound once, in `App\Providers\AppServiceProvider`:

```php
$this->app->singleton(AggregatorRegistry::class, fn () => new AggregatorRegistry([
    new QcmAggregator(),
    new NumericAggregator(),
    new DateAggregator(),
]));
```

**To add a new question type:** create a class implementing
`AggregatorInterface`, add it to that array. Nothing else changes — not the
controller, not the service, not the registry's logic.

## Running the tests

```bash
php artisan test
```

- `tests/Unit/Aggregation/` — one test class per aggregator, verified against
  the real XX1 data (files 1, 2, 12, 13, 14: qcm counts and the 697.2 numeric
  average), plus the registry's dispatch/unknown-type behavior.
- `tests/Unit/Repositories/` — `SurveyRepository` against a faked disk.
- `tests/Unit/Services/` — `SurveyAggregationService` orchestration.
- `tests/Feature/SurveyControllerTest.php` — `GET /api/list.json`,
  `GET /api/XX1.json` (200 + shape), and an unknown code (404).
