---
applyTo: '**'
---
# Changelog Instructions

## Partial Changelogs
Write and store partial changelogs to the `.changelogs/` folder if the current git branch is prefixed with `fix/` or `feat/`.
The changelog filename must include the current date (YYYY-MM-DD), the branch prefix and a short description, e.g. `2023-10-05-fix-some-bug.md` or `2023-10-05-feat-new-feature.md`.
The content of the changelog file must follow this template:

```markdown
--- 
title: <Short description>
issue: <GitHub issue number if applicable, e.g. #123>
---
# <Type>: <Short description>
* <Describe the change in simple and short sentence(s)>
```

## Full Changelog
When merging a branch prefixed with `fix/` or `feat/` into `master` or if `master` is the active branch, the partial changelogs from the `.changelogs/` folder will be combined into a full changelog entry and appended to the `CHANGELOG.md` file in the repository root.
In addition, the full changelog will be translated to German and added to the `CHANGELOG_de-DE.md` file in the repository root.

## Writing Style Guidelines
Keep changelog entries **simple, concise, and user-focused**:
- Write from the user's perspective, describing what was fixed or improved
- Avoid technical implementation details (CSS selectors, template paths, etc.)
- Use clear, straightforward language
- Keep entries short (one line when possible)
- Dont include issue numbers or references at all

**Good examples:**
- ✅ "Fixed bar-style pagination bullets not displaying correctly"
- ✅ "Fixed empty breakpoint values in responsive slide images"
- ✅ "Slides can now be copied again without any issues"

**Avoid:**
- ❌ "Fixed CSS selector for bar-style pagination bullets to correctly apply aspect ratio when bar shape is selected"
- ❌ "Fixed empty breakpoint values in picture element media queries by removing incorrect breakpoint redefinition in cover-image template"