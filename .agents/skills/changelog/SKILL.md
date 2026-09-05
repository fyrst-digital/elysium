---
name: changelog
description: Creates partial changelogs on feat/fix branches and folds them into CHANGELOG.md plus CHANGELOG_de-DE.md. Use when adding changelog entries, finishing a feature or fix, merging to master, or when the user mentions changelog, CHANGELOG.md, or .changelogs/.
---

# Changelog

## Partial changelogs (`feat/` / `fix/` branches)

Write one file in `.changelogs/` named `YYYY-MM-DD-{feat|fix}-slug.md`.

```markdown
---
title: <Short description>
issue: <GitHub issue number if applicable, e.g. #123>
---
# <Type>: <Short description>
* <Describe the change in simple and short sentence(s)>
```

`<Type>` is `Feat` or `Fix`. Put the GitHub issue in YAML only.

## Full changelog (`master` or merging into `master`)

Combine `.changelogs/*.md` into the `# Unreleased` section of `CHANGELOG.md`, then the German equivalent in `CHANGELOG_de-DE.md`. Existing format:

```markdown
# Unreleased

**Changelog**
- User-facing English bullet
```

On release, rename `# Unreleased` to `# x.y.z` (version from `composer.json`). Do not invent a different generator. HTML for the Shopware store is `npm run md:changelog` in CI.

## Style

User-focused, one line when possible. No CSS selectors, template paths, class names, or issue numbers in the body.

Good:

- "Fixed bar-style pagination bullets not displaying correctly"
- "Slides can now be copied again without any issues"

Avoid:

- "Fixed CSS selector for bar-style pagination bullets to correctly apply aspect ratio when bar shape is selected"
