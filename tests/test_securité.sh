- name: Security scan (basic)
  run: |
    echo "🔒 Scan de sécurité basique..."
    if grep -R "password" -n .; then
      echo "⚠️ Mot 'password' trouvé dans le dépôt !"
    else
      echo "🔐 OK - Pas de mot de passe exposé"
    fi