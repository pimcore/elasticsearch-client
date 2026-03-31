# Upgrade Information

Following steps are necessary during updating to newer versions.

## Upgrade to 2026.1.0

### PHP and Dependency Requirements

- Added support for `PHP` `8.5`.
- Removed support for `PHP` `8.3` and Symfony `v6`.

### New Features

- **DSN-based Elasticsearch configuration**: The client can now be configured via a single DSN string
  using the `PIMCORE_ELASTICSEARCH_DSN` environment variable
  (e.g. `elasticsearch://elastic:secret@elasticsearch:9200?ssl=false&ssl_verify=false`).
  The DSN is parsed at runtime and overrides individual `hosts`, `username`, `password`, and
  `ssl_verification` settings. SSL defaults to `false` (HTTP) when using a DSN.
  The `dsn` key is now available in the bundle configuration per client.
