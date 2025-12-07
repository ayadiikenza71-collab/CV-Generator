- name: Check Dockerfile
  run: |
    if [ ! -s Dockerfile ]; then
      echo "❌ Dockerfile introuvable ou vide"
      exit 1
    else
      echo "✅ Dockerfile OK"
    fi
