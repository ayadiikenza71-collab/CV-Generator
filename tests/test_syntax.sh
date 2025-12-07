#!/bin/bash

echo "🔎 Vérification de la syntaxe PHP..."

ERROR=0

for file in $(find . -name "*.php"); do
    php -l "$file"
    if [ $? -ne 0 ]; then
        ERROR=1
    fi
done

if [ $ERROR -ne 0 ]; then
    echo "❌ Erreurs détectées"
    exit 1
else
    echo "✅ Aucun problème détecté"
    exit 0
fi