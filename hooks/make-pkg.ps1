#!/usr/bin/env pwsh
# Create the release package.
$tag=@(git describe --abbrev=0 --tags)
tar -a -cf "activation-requirements-wp.$tag.zip" config src composer.json LICENSE README.md
