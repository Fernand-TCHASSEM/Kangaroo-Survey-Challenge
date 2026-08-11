# Kangaroo Survey Challenge — Frontend

Vue 3 + Vite single-page app that lists surveys by store and shows their
aggregated results, pulled from the Laravel API in `../backend`.

## Requirements

- Node 22+
- The backend API running (see `../backend/README.md`)

## Running the frontend

```bash
npm install
cp .env.example .env
npm run dev
```

By default the app expects the API at `http://localhost:8000` — adjust
`VITE_API_BASE` in `.env` if your backend runs elsewhere (e.g. behind a proxy
host).

## Environment variables (`.env`)

| Variable                | Purpose                                                          |
| ------------------------ | ----------------------------------------------------------------- |
| `VITE_API_BASE`          | Base URL of the backend API (no trailing slash).                 |
| `VITE_ALLOWED_HOSTS`     | Comma-separated hosts Vite's dev server accepts (proxy setups).  |
| `VITE_HMR_HOST`          | Host used for the hot-module-reload websocket.                   |
| `VITE_HMR_CLIENT_PORT`   | Port the HMR client connects back to.                             |

`.env` is not committed (it holds machine-local values); `.env.example` is the
template to copy from.

## How it works

- `src/composables/useSurveys.js` — the only place that talks to the API.
  Exposes `fetchSurveys()` / `fetchResults(code)` plus their own
  `loading`/`error` refs, reading `VITE_API_BASE` and calling
  `GET /api/list.json` and `GET /api/{code}.json`.
- `src/components/SurveyList.vue` — renders the fetched surveys, with a
  client-side search box filtering by name or code.
- `src/components/SurveyDetail.vue` — renders the aggregated results for the
  selected code: `qcm` questions as bars, `numeric` questions as an average,
  and a loading state / a distinct "not found" message on a 404.
- `src/App.vue` — wires the two components together and owns which code is
  currently selected.

## Building for production

```bash
npm run build
```

Outputs to `dist/`.
