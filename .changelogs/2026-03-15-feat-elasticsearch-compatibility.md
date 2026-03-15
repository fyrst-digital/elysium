---
title: Elasticsearch compatibility for admin search
issue: #50
---
# Feat: Elasticsearch compatibility for admin search

* Added `ElysiumSlidesAdminSearchIndexer` to enable Elasticsearch indexing for elysium slides in the administration
* Added `SearchRanking` flags to `name` (HIGH) and `title` (MIDDLE) fields for better search relevance
* Feature is gated behind the `elysium_preview_elasticsearch` feature flag (technical preview)
* Requires `SHOPWARE_ADMIN_ES_ENABLED=1` environment variable to activate Elasticsearch in admin
