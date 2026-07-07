# Security Policy

## Supported versions

Panulat currently supports the latest tagged release.

Because Panulat is still in early development, users are encouraged to upgrade to the latest available version for security fixes and improvements.

## Reporting a vulnerability

Please report suspected security issues privately to the project maintainer instead of opening a public issue with exploit details.

Include:

-- impact and suggested mitigation, if known

## Production safety

Use `APP_ENV=production` with `APP_DEBUG=false`.

Panulat intentionally refuses unsafe production debug configuration and renders production errors without stack traces.