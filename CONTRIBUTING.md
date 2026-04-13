# Contributing

Contributions are welcome! Here's how to get started.

## Setup

```bash
git clone https://github.com/your-vendor/symfony-skeleton.git
cd symfony-skeleton
make install-dev
make build
make up
make db-reset
```

## Workflow

1. **Fork** the repository and create a branch from `main`
2. **Write tests** for any new feature or bug fix
3. **Run the full quality suite** before submitting:

```bash
make lint
make cs-fix
make analyse
make test
make security
```

4. **Commit** using [Conventional Commits](https://www.conventionalcommits.org):
   - `feat: add user export feature`
   - `fix: correct redirect after login`
   - `docs: update README setup steps`
   - `test: add voter unit tests`
   - `refactor: extract pagination service`

5. **Open a Pull Request** against `main` and fill in the PR template

## Code Style

This project uses **PHP-CS-Fixer** with Symfony rules + PHP 8.2 migrations:

```bash
make cs-fix   # auto-fix
make cs-check # dry-run
```

## Static Analysis

PHPStan runs at **level 8**:

```bash
make analyse
```

## Tests

```bash
make test           # full suite
make test-unit      # unit only
make test-coverage  # with HTML coverage
```

## Branch Strategy

| Branch    | Purpose                    |
|-----------|----------------------------|
| `main`    | Stable, tagged releases    |
| `develop` | Integration branch         |
| `feat/*`  | Feature branches           |
| `fix/*`   | Bug fix branches           |

## Release

Releases are created automatically via GitHub Actions when a tag is pushed:

```bash
git tag v1.2.0
git push origin v1.2.0
```

The changelog is generated from conventional commits using `git-cliff`.
