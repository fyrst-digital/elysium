---
issue: #57
---
# Fix: Shopware 6.7 migration compatibility for product foreign key

* Updated Migration1706795531AddSlideProduct to use composite foreign key referencing both product.id and product.version_id
* Added product_version_id field to ElysiumSlidesDefinition entity
* Added productVersionId property to ElysiumSlidesEntity with getter/setter

This fixes the installation error on fresh Shopware 6.7.x installations where the product table uses a composite primary key (id, version_id).