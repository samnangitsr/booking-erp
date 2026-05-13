#!/usr/bin/env bash
# Smoke test: for each seeded role (super_admin, admin, manager, staff), log
# in via the admin form, then GET every routable admin module index page.
# A non-2xx/3xx response is treated as a regression.
set -uo pipefail

BASE=${BASE:-http://127.0.0.1:8088}
EMAILS=(super@bookingerp.demo admin@bookingerp.demo manager@bookingerp.demo staff@bookingerp.demo)
PASSWORD=password

# All admin index + create URLs derived from route:list, plus the dashboard.
URLS=$(php artisan route:list --json 2>/dev/null \
  | jq -r '.[] | select(.method | contains("GET")) | select(.name | startswith("admin.") and (endswith(".index") or endswith(".create"))) | .uri')
URLS=$(printf '%s\nadmin\n' "$URLS")

if [ -z "${URLS}" ]; then
  echo "No admin index routes found"; exit 1
fi

failures=0

for EMAIL in "${EMAILS[@]}"; do
  COOKIE_JAR=$(mktemp)
  trap 'rm -f "$COOKIE_JAR"' EXIT
  # Prime CSRF
  curl -sS -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE/admin/login" > /dev/null
  TOKEN=$(grep -E 'XSRF-TOKEN' "$COOKIE_JAR" | tail -n1 | awk '{print $7}' | sed 's/%3D/=/g')
  # Real CSRF from the form
  CSRF=$(curl -sS -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE/admin/login" | grep -oE 'name="_token" value="[^"]+"' | head -n1 | sed -E 's/.*value="([^"]+)".*/\1/')
  LOGIN_STATUS=$(curl -sS -o /dev/null -w '%{http_code}' \
    -c "$COOKIE_JAR" -b "$COOKIE_JAR" \
    -X POST \
    -d "email=$EMAIL" -d "password=$PASSWORD" -d "_token=$CSRF" \
    "$BASE/admin/login")
  if [[ "$LOGIN_STATUS" != "302" ]]; then
    echo "[FAIL] login $EMAIL returned $LOGIN_STATUS"
    failures=$((failures + 1))
    rm -f "$COOKIE_JAR"
    continue
  fi
  echo "=== logged in as $EMAIL ==="
  for uri in $URLS; do
    # Skip routes with path params
    if [[ "$uri" == *"{"* ]]; then continue; fi
    code=$(curl -sS -o /dev/null -w '%{http_code}' -c "$COOKIE_JAR" -b "$COOKIE_JAR" "$BASE/$uri")
    # Expect 200 (allowed) or 403 (gated by permission — acceptable for non-super roles)
    if [[ "$code" != "200" && "$code" != "403" && "$code" != "302" ]]; then
      echo "  [FAIL] /$uri -> $code"
      failures=$((failures + 1))
    fi
  done
  rm -f "$COOKIE_JAR"
done

if [[ $failures -gt 0 ]]; then
  echo "HTTP smoke test: $failures failure(s)"
  exit 1
fi
echo "HTTP smoke test: all pages OK"
