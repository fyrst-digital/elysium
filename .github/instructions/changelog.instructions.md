---
applyTo: '**'
---
# Changelog Instructions
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