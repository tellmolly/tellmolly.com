#!/usr/bin/env bash
# Runtime bootstrap for the php-fpm container: wait for the database, run the
# migrations, then hand off to php-fpm. Runs as dockeruser.
#
# The init and scheduler containers override this entrypoint, so only the app
# container migrates. A failed migration stops the container instead of serving
# new code against an old schema; Docker's restart policy then tries again.
set -euo pipefail
cd /app

# After a server reboot MariaDB may still be starting (up to ~60s).
for i in $(seq 1 30); do
  # Only opens a connection; db:show would also need the intl extension.
  if php artisan tinker --execute "DB::connection()->getPdo();" >/dev/null 2>&1; then break; fi
  echo "[entrypoint] waiting for database... ($i)"
  sleep 2
done

php artisan migrate --force

# Preserve the base php image's entrypoint, then run the given command (php-fpm).
exec docker-php-entrypoint "$@"
