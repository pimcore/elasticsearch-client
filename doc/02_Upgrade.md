# Upgrade Information
Following steps are necessary during updating to newer versions.

## Upgrade to 2026.3.0
- Raised the minimum required `elasticsearch/elasticsearch` client library version to `^8.12`.
  Elasticsearch server `8.12.2` or higher is now the minimum supported version.
  (The PHP client is released per minor version only, so the server patch requirement cannot be
  expressed in composer — make sure your cluster runs at least `8.12.2`.)

## Upgrade to 2026.1.0
- Added DSN-based configuration support. The Elasticsearch client can now be configured via a single DSN string using the `PIMCORE_ELASTICSEARCH_DSN` environment variable (e.g. `elasticsearch://elastic:secret@elasticsearch:9200?ssl=false&ssl_verify=false`). The DSN is parsed at runtime in the client factory and overrides individual `hosts`, `username`, `password`, and `ssl_verification` settings. SSL defaults to `false` (HTTP) when using DSN.
- Added support to `PHP` `8.5`.
- Removed support to `PHP` `8.3` and Symfony `v6`.
