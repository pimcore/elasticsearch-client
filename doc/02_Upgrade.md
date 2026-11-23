# Upgrade Information
Following steps are necessary during updating to newer versions.

## Upgrade to 2026.4.0
- The `elasticsearch/elasticsearch` client library constraint is now `^8.12 || ^9.0`, so projects can run the
  9.x client. Nobody is forced onto it, but the next `composer update` resolves the newest allowed version:
  **projects on an Elasticsearch 8 cluster must pin `elasticsearch/elasticsearch` to `^8.12` in their own
  composer.json before updating**, otherwise Composer installs the 9.x client, which that cluster rejects.
  The bundle's factory and `SearchClient` adapter use the same API on both majors.
- **Staying on Elasticsearch 8:** add the pin to the project's root `composer.json` before the next
  `composer update`, otherwise Composer resolves the 9.x client:

  ```json
  {
      "require": {
          "elasticsearch/elasticsearch": "^8.12"
      }
  }
  ```

- Pick the client major that matches your Elasticsearch server major. Elastic clients are only forward
  compatible within one major: a 9.x client is rejected by an 8.x cluster (HTTP 406), while an 8.x client
  still talks to a 9.x cluster through Elastic's REST API compatibility mode (one major back, logged under
  `compatible_api` in the cluster's deprecation log). Upgrade the cluster to the last 8.x release before
  moving it to 9.
- The 9.x client ships with `elastic/transport` 9, which no longer depends on Guzzle. It discovers an installed
  PSR-18 client (Guzzle, Symfony HttpClient) through `php-http/discovery` and falls back to its built-in cURL
  client otherwise. Verify that any configured `http_options` still apply with the client that is discovered.
- Elasticsearch 9.1.7 / 9.2 and later exclude `dense_vector` values from `_source` of newly created indices
  (`index.mapping.exclude_source_vectors`). Code that reads vectors back from `_source` must request them via
  `fields` or `_source.exclude_vectors: false`.

## Upgrade to 2026.3.0
- Raised the minimum required `elasticsearch/elasticsearch` client library version to `^8.12`.
  Elasticsearch server `8.12.2` or higher is now the minimum supported version.
  (The PHP client is released per minor version only, so the server patch requirement cannot be
  expressed in composer — make sure your cluster runs at least `8.12.2`.)

## Upgrade to 2026.1.0
- Added DSN-based configuration support. The Elasticsearch client can now be configured via a single DSN string using the `PIMCORE_ELASTICSEARCH_DSN` environment variable (e.g. `elasticsearch://elastic:secret@elasticsearch:9200?ssl=false&ssl_verify=false`). The DSN is parsed at runtime in the client factory and overrides individual `hosts`, `username`, `password`, and `ssl_verification` settings. SSL defaults to `false` (HTTP) when using DSN.
- Added support to `PHP` `8.5`.
- Removed support to `PHP` `8.3` and Symfony `v6`.
