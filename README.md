# Kangaroo Survey Challenge

A small full-stack app that aggregates in-store survey answers stored as JSON
files. A salesperson fills one JSON file per visit; files are grouped by a
`code` (one per store — XX1 = Paris, XX2 = Chartres, XX3 = Melun), and each
question is aggregated across every file sharing that code. The frontend lists
the stores, lets you search them, and shows the aggregated results for the one
you pick.

```
survey_challenge/
  backend/   # Laravel API — aggregation logic + endpoints
  frontend/  # Vue 3 + Vite SPA — list, search, results
```

## Running it

- Backend: see [`backend/README.md`](backend/README.md) (`composer install`,
  `php artisan serve`).
- Frontend: see [`frontend/README.md`](frontend/README.md) (`npm install`,
  `npm run dev`). Point `VITE_API_BASE` at wherever the backend is running.

## Where the data lives

The 15 survey JSON files are committed at `backend/storage/app/data/` — they
are the persistence layer for this app, no database is involved. They're read
through a dedicated `surveys` filesystem disk rather than Laravel's default
`local` disk; details in `backend/README.md`.

## Design notes: aggregation via the Strategy pattern

The one hard requirement here is that adding a new question type must never
require touching the controller. The backend has one class per question type
(`App\Aggregation\{Qcm,Numeric,Date}Aggregator`, all implementing
`AggregatorInterface`), and an `AggregatorRegistry` that picks the right one
for a given type. `SurveyController` only calls
`App\Services\SurveyAggregationService`, which asks the registry for the
aggregator to use — it never branches on question type itself.

To add a new question type: write a class implementing `AggregatorInterface`
and add it to the array bound in `AppServiceProvider`. Nothing else changes.

Full writeup, including the storage-disk rationale, in
[`backend/README.md`](backend/README.md).

## Tests

```bash
cd backend
php artisan test
```

Unit tests cover each aggregator (verified against the real XX1 data), the
registry's dispatch/unknown-type behavior, the repository, and the
aggregation service; feature tests cover both API endpoints, including the
404 case. See [`backend/README.md`](backend/README.md) for the breakdown.
