#!/usr/bin/env bash
set -euo pipefail

if ! git rev-parse --is-inside-work-tree >/dev/null 2>&1; then
  echo "[hata] Bu komut yalnızca bir Git deposu içinde çalıştırılabilir." >&2
  exit 2
fi

if ! git diff --cached --quiet --exit-code >/dev/null 2>&1 || ! git diff --quiet --exit-code >/dev/null 2>&1; then
  :
fi

binary_files=()
while IFS=$'\t' read -r added deleted path; do
  [[ -z "$path" ]] && continue
  if [[ "$added" == "-" || "$deleted" == "-" ]]; then
    binary_files+=("$path")
    continue
  fi
  if [[ -f "$path" ]]; then
    if LC_ALL=C grep -Iq . "$path"; then
      continue
    else
      binary_files+=("$path")
    fi
  fi
done < <(git diff --cached --numstat)

if (( ${#binary_files[@]} == 0 )); then
  echo "✅  İkili dosya bulunamadı."
  exit 0
fi

echo "❌  Aşağıdaki dosyalar ikili görünüyor:" >&2
for file in "${binary_files[@]}"; do
  echo "  - $file" >&2
  git check-attr diff "$file" 2>/dev/null || true
  git check-attr text "$file" 2>/dev/null || true
  git check-attr working-tree-encoding "$file" 2>/dev/null || true
  echo >&2
fi

echo "Lütfen bu dosyaları metin tabanlı bir formata çevirin veya .gitattributes ile diff tanımı yapın." >&2
exit 1
