#!/usr/bin/env bash
# TUDAK Afet Yönetim Sistemi - Toplu Kalite Kontrolleri
# Bu script, PR hazırlık sürecinde kullanılan kalite kontrollerini ardışık olarak çalıştırır.
# Komutlar mevcut değilse adımlar atlanır fakat sonuç raporlanır.

set -u -o pipefail

if ! command -v git >/dev/null 2>&1; then
  echo "git komutu bulunamadı. Script depo kökünde çalıştırılmalıdır." >&2
  exit 1
fi

ROOT_DIR="$(git rev-parse --show-toplevel 2>/dev/null || pwd)"
cd "$ROOT_DIR" || exit 1

STATUS=0

print_header() {
  local title="$1"
  printf '\n\033[1m=== %s ===\033[0m\n' "$title"
}

run_step() {
  local title="$1"
  shift
  local cmd=("$@")

  print_header "$title"
  if "${cmd[@]}"; then
    echo "✅ $title tamamlandı"
  else
    local exit_code=$?
    echo "❌ $title başarısız (kod: $exit_code)"
    STATUS=1
  fi
}

run_if_executable() {
  local title="$1"
  local executable="$2"
  shift 2
  local args=("$@")

  if [ -x "$executable" ]; then
    run_step "$title" "$executable" "${args[@]}"
  else
    echo
    echo "⚠️  $title atlandı: $executable bulunamadı"
  fi
}

run_if_command() {
  local title="$1"
  local command_name="$2"
  shift 2
  local args=("$@")

  if command -v "$command_name" >/dev/null 2>&1; then
    run_step "$title" "$command_name" "${args[@]}"
  else
    echo
    echo "⚠️  $title atlandı: $command_name komutu mevcut değil"
  fi
}

# PHP kalite araçları için composer bağımlılıklarını gerekirse kur
needs_composer_install=0
for php_tool in php-cs-fixer phpcs phpstan psalm; do
  if [ ! -x "$ROOT_DIR/vendor/bin/$php_tool" ]; then
    needs_composer_install=1
    break
  fi
done

if [ "$needs_composer_install" -eq 1 ]; then
  if command -v composer >/dev/null 2>&1; then
    run_step "Composer bağımlılık kurulumu" composer install --no-ansi --no-interaction --no-progress --prefer-dist
  else
    echo
    echo "⚠️  Composer bağımlılık kurulumu atlandı: composer komutu bulunamadı"
  fi
fi

BACKEND_DIR="$ROOT_DIR/backend"
if [ -d "$BACKEND_DIR" ] && [ -f "$BACKEND_DIR/composer.json" ]; then
  needs_backend_install=0
  if [ ! -d "$BACKEND_DIR/vendor" ]; then
    needs_backend_install=1
  elif [ ! -f "$BACKEND_DIR/vendor/autoload.php" ]; then
    needs_backend_install=1
  fi

  if [ "$needs_backend_install" -eq 1 ]; then
    if command -v composer >/dev/null 2>&1; then
      run_step "Backend Composer bağımlılık kurulumu" composer --working-dir="$BACKEND_DIR" install --no-ansi --no-interaction --no-progress --prefer-dist
    else
      echo
      echo "⚠️  Backend Composer bağımlılık kurulumu atlandı: composer komutu bulunamadı"
    fi
  fi
fi

# Frontend lint araçları için npm bağımlılıklarını gerekirse kur
if [ -f "$ROOT_DIR/package.json" ]; then
  needs_npm_install=0
  if [ ! -d "$ROOT_DIR/node_modules" ]; then
    needs_npm_install=1
  elif [ ! -x "$ROOT_DIR/node_modules/.bin/eslint" ] || [ ! -x "$ROOT_DIR/node_modules/.bin/stylelint" ]; then
    needs_npm_install=1
  fi

  if [ "$needs_npm_install" -eq 1 ]; then
    if command -v npm >/dev/null 2>&1; then
      run_step "npm bağımlılık kurulumu" npm install --no-audit --progress false
    else
      echo
      echo "⚠️  npm bağımlılık kurulumu atlandı: npm komutu bulunamadı"
    fi
  fi
fi

# 1. İkili dosya taraması (varsa)
if [ -x "$ROOT_DIR/tools/check-binary-files.sh" ]; then
  run_step "İkili dosya taraması" "$ROOT_DIR/tools/check-binary-files.sh"
else
  echo "⚠️  İkili dosya taraması atlandı: tools/check-binary-files.sh bulunamadı"
fi

# 2. PHP format kontrolleri
run_if_executable "PHP-CS-Fixer (dry-run)" "$ROOT_DIR/vendor/bin/php-cs-fixer" fix --config=.php-cs-fixer.dist.php --dry-run --diff

# 3. PHP_CodeSniffer
run_if_executable "PHP_CodeSniffer" "$ROOT_DIR/vendor/bin/phpcs" --standard=phpcs.xml

# 4. PHPStan
run_if_executable "PHPStan" "$ROOT_DIR/vendor/bin/phpstan" analyse -c phpstan.neon.dist --memory-limit=512M

# 5. Psalm
run_if_executable "Psalm" "$ROOT_DIR/vendor/bin/psalm" --config=psalm.xml
run_if_executable "PHPStan" "$ROOT_DIR/vendor/bin/phpstan" analyse -c phpstan.neon.dist

# 5. Psalm
run_if_executable "Psalm" "$ROOT_DIR/vendor/bin/psalm"

# 6. Node.js lint komutları
if [ -f "$ROOT_DIR/package.json" ]; then
  run_if_command "npm run lint" npm run lint
else
  echo
  echo "⚠️  npm lint adımı atlandı: package.json bulunamadı"
fi

exit "$STATUS"
