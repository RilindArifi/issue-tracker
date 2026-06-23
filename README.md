# Mini Issue Tracker

A small but complete issue-tracking application built with **Laravel**, **Blade**, and
**Alpine.js**. Projects own issues; issues carry a status, priority, tags, assigned
members, and comments. Tag/member management, comment loading and live search all happen
over AJAX without full page reloads.

Built as a technical-interview exercise, with an emphasis on clean Laravel conventions,
N+1-free queries, server-rendered partials reused on the client, and a logical commit
history.

---

## Tech stack

| Layer      | Choice                                                        |
| ---------- | ------------------------------------------------------------- |
| Framework  | Laravel 13 · PHP 8.3+                                          |
| Auth       | Laravel Breeze (Blade stack)                                  |
| Frontend   | Blade + Alpine.js 3 + native `fetch` (no jQuery, no SPA)      |
| Styling    | Tailwind CSS (via Vite)                                       |
| Database   | MySQL (development) · SQLite `:memory:` (tests)               |
| Tests      | PHPUnit                                                        |

---

## Features

- **Projects** — full CRUD with owner, description, start date and deadline.
- **Issues** — full CRUD with status / priority enums, due date, tags and members.
- **Filtering** — issues filterable by status, priority and tag using Eloquent query scopes.
- **Live search** — debounced (300 ms) full-text search over issue title/description, via AJAX.
- **Tags** — attach / detach on an issue over AJAX, no reload.
- **Comments** — paginated "load more" plus AJAX create that prepends the new comment.
- **Members (bonus)** — assign / unassign users to an issue over AJAX.
- **Authorization (bonus)** — `ProjectPolicy` restricts update/delete to the project owner.
- **Validation** — all writes go through Form Requests; AJAX endpoints return `422` JSON
  with errors rendered inline (no `alert()`).

---

## Architecture notes

- **No N+1 queries.** List/show actions eager-load relations (`with(...)`, `withCount(...)`).
  Verified with `DB::listen` — the issues index runs a constant ~4 queries regardless of row count.
- **One source of markup.** Comment rows, issue rows and tag badges live in Blade partials
  (`resources/views/partials/`). The AJAX endpoints render those same partials server-side via
  `view()->render()`, so HTML is never duplicated in JavaScript.
- **AJAX validation.** Form Requests for AJAX-only endpoints override `failedValidation()` to
  always return `422` JSON instead of redirecting, so the frontend can show inline errors.
- **Shared fetch helper.** `resources/js/app.js` exposes a small `apiFetch()` wrapper that
  injects the CSRF token and normalises `422` responses for every Alpine component.

---

## Requirements

- PHP **8.3+**
- Composer
- Node.js 18+ and npm
- MySQL 8+ (or MariaDB)

---

## Getting started

```bash
# 1. Install dependencies
composer install
npm install

# 2. Environment
cp .env.example .env
php artisan key:generate
```

Configure the database in `.env` (defaults shown):

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=issue_tracker
DB_USERNAME=root
DB_PASSWORD=
```

Create the database, then run migrations + seeders:

```bash
php artisan migrate --seed
```

Build assets and serve:

```bash
npm run build          # or: npm run dev  (for hot reloading)
php artisan serve      # or use Laravel Herd / Valet
```

> Using **Laravel Herd**? The app is served automatically at `http://issue-tracker.test`.

### Demo accounts

The seeder creates two users (password is `password` for both):

| Email                | Role                |
| -------------------- | ------------------- |
| `test@example.com`   | Owner of 3 projects |
| `jane@example.com`   | Teammate            |

The seed data includes 8 tags and 3 projects fully populated with issues, tags, members
and comments.

---

## Running tests

Tests use an in-memory SQLite database (configured in `phpunit.xml`), so no MySQL setup is
required to run them:

```bash
php artisan test
```

The feature suite covers project & issue CRUD, validation, the `ProjectPolicy`, and the
AJAX endpoints (tags attach/detach, member assignment, comment create/validation/pagination,
and live search).

---

## Project structure

```
app/
├── Enums/                 IssueStatus, IssuePriority (label(), color(), options())
├── Http/
│   ├── Controllers/       Project, Issue, Tag, Comment, Member
│   └── Requests/          Form Requests (Store/Update + AJAX 422 variants)
├── Models/                Project, Issue, Tag, Comment, User
└── Policies/              ProjectPolicy

resources/
├── js/app.js              apiFetch() helper + Alpine components
│                          (issueTags, issueMembers, issueComments, issueSearch)
└── views/
    ├── projects/          index, create, edit, show, _form
    ├── issues/            index, create, edit, show, _form
    └── partials/          comment-item, tag-badge, issue-row, flash

database/
├── migrations/            projects, tags, issues, comments, pivots, project dates
├── factories/             one per model
└── seeders/               DatabaseSeeder

tests/Feature/             Project, ProjectPolicy, Issue, IssueSearch,
                           TagAjax, CommentAjax, MemberAjax
```

---

## Key routes

| Method      | URI                                  | Purpose                          |
| ----------- | ------------------------------------ | -------------------------------- |
| resource    | `/projects`                          | Project CRUD                     |
| resource    | `/issues`                            | Issue CRUD                       |
| `GET`       | `/issues/search`                     | AJAX live search / filter        |
| `POST`      | `/issues/{issue}/tags`               | Attach a tag (AJAX)              |
| `DELETE`    | `/issues/{issue}/tags/{tag}`         | Detach a tag (AJAX)             |
| `POST`      | `/issues/{issue}/members`            | Assign a member (AJAX)          |
| `DELETE`    | `/issues/{issue}/members/{user}`     | Unassign a member (AJAX)        |
| `GET`       | `/issues/{issue}/comments`           | Paginated comments (AJAX)       |
| `POST`      | `/issues/{issue}/comments`           | Create a comment (AJAX)         |

All application routes are behind the `auth` middleware.
