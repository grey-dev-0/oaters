Intentionally empty: Tests\TestCase::migrateFreshUsing() points migrate:fresh at this
directory so the central schema (database/migrations) is skipped in favor of the module
schema, which owns the same permission tables in tenant databases.
