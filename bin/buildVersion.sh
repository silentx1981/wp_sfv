#!/usr/bin/bash

# Parameter
version="$1"

# Source-/Target-Directory
srcDir="./"
destDir="versions/$version"
zipFile="versions/${version}.zip"

# Ausgeschlossene Dateien/Ordner
exclude_patterns=(".idea" ".git" "bin" "ci" "config/config.json" "coverage" "tests" "theme" "versions" ".gitignore" "composer.json" "composer.lock" "package.json" "package-lock.json" "README.md")

# Start-Message
echo "Build Version $version ...";

# Create DestDir
echo "Erstelle Ziel-Ordner"
mkdir -p "$destDir"

# Kopieren die Dateien
echo "Kopiere benötigte Dateien"
rsync -avq $(printf -- "--exclude=%s " "${exclude_patterns[@]}") "$srcDir" "$destDir"

# Zippe den Ordner
echo "Packe die Dateien"
zip -rq "$zipFile" "$destDir"

# Löschen vom Temporären Ordner
echo "Lösche die Dateien, ausser die gepackte Datei"
rm -R "$destDir"

echo 'Abgeschlossen'




