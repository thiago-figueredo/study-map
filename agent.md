# Agent instructions (PHP / Laravel)

Context for the **study-map** API under this directory.

## Stack

- PHP **^8.3**, Laravel **^13**, Pest **^4** (`composer.json`).
- Sanctum is installed; routes in `routes/api.php` are **not** authenticated yet.
- Feature tests use `Tests\TestCase` (PHPUnit-style); Pest binds that base class for `tests/Feature` (`tests/Pest.php`).

## Naming

- **Variables** (including parameters and closure captures): `snake_case` (e.g. `$questions_payload`, `$answers_to_create`).
- **Classes**: `StudlyCase` / PascalCase (e.g. `QuizService`, `CreateQuizRequest`).
- **Functions and methods**: `camelCase` (e.g. `firstOrCreate`, `createMany`).

Apply these rules consistently in new and edited PHP code in this project.

## HTTP API

- Laravel registers `routes/api.php` with the **`/api`** prefix (`bootstrap/app.php`).
- Endpoints today (`routes/api.php`):
  - `GET /api/quizzes` — `QuizController@index`
  - `POST /api/quizzes` — `QuizController@store`
- JSON responses use Laravel **JsonResource** wrapping: single resources resolve under a top-level **`data`** key; collections use **`data`** as an array of items (see `ListQuizzesTest`, `CreateQuizTest`).

## Domain model

- **Quiz** (`name`): `hasMany` **Question**s; `morphToMany` **Tag** via `tag_bind` (`binded` morph name).
- **Question** (`body`): `belongsTo` Quiz; `hasMany` **Answer**s; `morphToMany` Tag via `tag_bind`.
- **Answer** (`body`, `is_correct`): `belongsTo` Question. Creation for nested payloads is handled in `QuizService` (including `question_id` in persisted rows).
- **Tag** (`name`): shared table; quiz-level tags in `CreateQuizRequest` use `unique:tags,name` on `tags.*` (question-level tag strings are only `string|max:50`).
- Base Eloquent model: `App\Models\Model` — adds `HasFactory`, `HasTimestamps`, and a static `make()` helper.

## Application patterns

- **Controllers** stay thin: constructor-injected **services** for writes (`QuizController` + `QuizService`).
- **Validation**: `FormRequest` classes (e.g. `CreateQuizRequest`) — `authorize()` is currently `true` for store.
- **Responses**: `App\Http\Resources\*` extend `BaseResource`, which centralizes `id`, `created_at`, `updated_at`, `deleted_at` via `formatToArray()`. Nested shapes expose `jsonStructure()` for tests (`QuizResource`, `QuestionResource`, etc.). **TagResource** omits timestamp fields in the serialized output (see `TagResource::toArray`).
- **Persistence**: `QuizService::create()` creates the quiz, syncs quiz tags, `createMany` questions, syncs per-question tags when answers are present, then **upserts** answers on `['body', 'question_id']` updating `is_correct`, and returns the quiz `load()`ed with `questions.answers`, `questions.tags`, and `tags`.

## Tests

- Run: `vendor/bin/pest` (from this `api/` directory; optional filters: `vendor/bin/pest --filter=TestName`).
- `Tests\TestCase` uses `RefreshDatabase` and overrides `assertDatabaseHas()` to compare only **fillable** attributes on the given model class string (see `TestCase::assertDatabaseHas`).
- Helpers: `assertDatabaseHasOne`, `assertDatabaseHasMany`.
- Quiz feature tests live under `tests/Feature/Quiz/`; URLs use `/api/quizzes`.

## Files to touch for common tasks

| Task | Primary locations |
|------|-------------------|
| New route | `routes/api.php` |
| New endpoint logic | `app/Http/Controllers/`, `app/Services/` |
| Validation | `app/Http/Requests/` |
| JSON shape | `app/Http/Resources/` |
| Schema | `database/migrations/` |
| Feature tests | `tests/Feature/` |

Keep changes scoped; match existing style and resource/test patterns when extending the API.
