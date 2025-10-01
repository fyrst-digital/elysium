--- 
title: Fix empty breakpoint values in picture element media queries
issue: #44
---
# Fix: Fix empty breakpoint values in picture element media queries
* Removed incorrect `blurElysiumBreakpoints` redefinition in `cover-image.html.twig` that was overriding the correct breakpoint values from `base.html.twig`, causing empty pixel values in media attributes
