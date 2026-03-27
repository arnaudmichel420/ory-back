#!/usr/bin/env bash
# Crée les labels sur arnaudmichel420/ory-back
# Prérequis : gh auth login

REPO="arnaudmichel420/ory-back"

gh label create "feature" --color "0075ca" --description "Nouvelle fonctionnalité" --repo "$REPO" --force
gh label create "bug"     --color "d73a4a" --description "Quelque chose ne marche pas" --repo "$REPO" --force

echo "✅ Labels créés sur $REPO"
