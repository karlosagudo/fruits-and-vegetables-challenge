#!/usr/bin/env bash

git rev-parse --abbrev-ref HEAD | grep develop
if [ "$?" != "0" ]
then
  echo "You must run this script in develop branch."
  exit 1
fi

echo Updating changelog and creating new tag...
composer changelog
tag="$(jq '.version' composer.json | sed 's/\"//g')"
composer update --lock --no-scripts
read -p "Do you want to push the tag $tag? [y/N] " response
if [ "$response" != "y" ]
then
  echo "Insert process canceled by user. Restoring files: Changelog.md, composer.json"
  git restore --staged .
  git restore composer.json
  git restore composer.lock
  git restore CHANGELOG.md
  exit 0
fi
git add .
git commit -m "chore(release): update changelog for tag $tag" || exit 1
git tag $tag
git push && git push --tags
