---
title: Added Elysium demodata command
author: Robert Bisovski
---
# Feature: Added Elysium demodata command

* Added new `elysium:demodata` console command that generates demo data for Elysium slides, including placeholder cover images in the default Elysium Slide Builder media folder
* The same generator is auto-registered with the existing `framework:demodata` command via the `shopware.demodata_generator` tag, so `--elysium-slides=N` works there too
