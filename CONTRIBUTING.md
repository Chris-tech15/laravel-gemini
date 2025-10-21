# Contributing to Laravel Gemini

Thank you for considering contributing to **Laravel Gemini**!  
We welcome all kinds of contributions, including bug reports, feature requests, documentation improvements, and code contributions.

---

## How to Contribute

### 1. Fork the Repository
- Click the **Fork** button on the top right of the repository page.
- Clone your fork locally:
```bash
git clone https://github.com/hosseinhezami/laravel-gemini.git
cd laravel-gemini
````

### 2. Create a Feature Branch

* Always work on a new branch:

```bash
git checkout -b feature/your-feature-name
```
* Use descriptive branch names such as:

* `feature/add-cache`
* `fix/files-upload-bug`
* `docs/update-readme`

### 3. Install Dependencies

* Install required dependencies using Composer:

```bash
composer install
```

### 4. Run Tests

* Make sure all tests pass before submitting your changes:

```bash
php artisan test
```
* If you add new functionality, write corresponding tests.

### 5. Commit Guidelines

* Follow conventional commits where possible:

* `feat: add new builder feature`
* `fix: resolve caching issue in TextBuilder sync`
* `docs: update installation guide`

### 6. Push Changes

* Push your branch to your fork:

```bash
git push origin feature/your-feature-name
```

### 7. Submit a Pull Request

* Open a PR against the `master` branch.
* Clearly describe:

  * The purpose of your changes.
  * Any issues it fixes (e.g., `Fixes #12`).
  * Additional notes for reviewers.

---

## Code Style

* Follow **PSR-12** coding standards.
* Use **PHPStan** or **Laravel Pint** for static analysis and code style fixes.
* Keep functions small and focused.
* Write meaningful docblocks for public methods.

---

## Reporting Issues

If you find a bug, please open an issue with:

* A clear title.
* Steps to reproduce the bug.
* Expected vs actual behavior.
* Laravel version and PHP version.

---

## Community Guidelines

* Be respectful and collaborative.
* Avoid duplicate issues/PRs—search before creating new ones.
* Keep discussions constructive and focused on improving the project.

---

## License

By contributing, you agree that your contributions will be licensed under the [MIT License](LICENSE.md).
