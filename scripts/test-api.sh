#!/usr/bin/env sh
set -eu

BASE_URL="${BASE_URL:-http://127.0.0.1:8000}"

echo "Testing $BASE_URL/"
curl -fsS "$BASE_URL/"
echo "\n"

echo "Testing $BASE_URL/v1/health"
curl -fsS "$BASE_URL/v1/health"
echo "\n"

echo "Testing $BASE_URL/v1/ready"
curl -fsS "$BASE_URL/v1/ready"
echo "\n"

echo "Testing $BASE_URL/v1/users"
curl -fsS "$BASE_URL/v1/users"
echo "\n"

echo "Testing $BASE_URL/v1/users/with-profiles"
curl -fsS "$BASE_URL/v1/users/with-profiles"
echo "\n"
