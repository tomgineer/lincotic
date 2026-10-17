# Application tests

The test suite covers Lincotic's user-scoped hubs, access policy, editing services, Learn Mode documentation, cache isolation, metadata, models, sanitizers, and utility libraries.

Install the development dependencies and run the suite from the project root:

```shell
composer install
composer test
```

The default command does not collect code coverage, so it works without Xdebug or PCOV. To generate the configured coverage reports when a coverage driver is available, run:

```shell
composer test:coverage
```

Tests are grouped by purpose:

- `tests/unit` contains focused service, model, policy, sanitizer, and utility tests.
- `tests/integration` exercises complete multi-user HTTP flows, including visibility and Learn Mode behavior.
- `tests/_support` contains application-specific helpers used by the suite.

PHPUnit writes its generated logs, cache, and optional coverage reports under `build/`.
