#!/usr/bin/bash

# Parameter
version="${1:?Version fehlt, z.B. 1.2.3}"

# WICHTIG: Exakter Plugin-Ordnername (Slug) wie in wp-content/plugins/
plugin_slug="wp_sfv"

# Source-/Target-Directory
# Skript wird im Projektroot (Eltern von $plugin_slug) ausgeführt
srcDir="../$plugin_slug"
workDir="versions/$version"
destDir="$workDir/$plugin_slug"
zipFile="$workDir/${plugin_slug}-${version}.zip"

# Ausgeschlossene Dateien/Ordner
exclude_patterns=(
  ".idea" ".git" "bin" "ci" "config/config.json" "coverage" "tests"
  "theme" "versions" ".gitignore" "composer.json" "composer.lock"
  "package.json" "package-lock.json" "README.md"
)

echo "Build Version $version für $plugin_slug ..."

# Clean & Create work dir
rm -rf "$workDir"
mkdir -p "$destDir"

echo "Kopiere benötigte Dateien nach $destDir"
# Kopiere IN den Zielordner (Inhalt), aber der Zielordner heißt $plugin_slug
rsync -avq \
  $(printf -- "--exclude=%s " "${exclude_patterns[@]}") \
  "$srcDir/" "$destDir/"

echo "Packe ZIP mit Root-Ordner $plugin_slug"
(
  cd "$workDir"
  # ZIPPT DEN ORDNER $plugin_slug (nicht ./)
  zip -rq "$(basename "$zipFile")" "$plugin_slug"
)

echo "Aufräumen (Arbeitskopie behalten, ZIP im gleichen Ordner)"
# Wenn du die Arbeitskopie nicht brauchst, entkommentieren:
rm -rf "$destDir"

echo "Fertig: $zipFile"




