#!/bin/bash
# Run migrations before Apache
php protected/yii migrate/up --interactive=0 || true
php protected/yii migrate --migrationPath=@connectabroad/migrations --interactive=0 || true
php protected/yii cache/flush-all || true
exec "$@"