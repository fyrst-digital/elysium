import type { ShopwareClass } from 'src/core/shopware'
import '@shopware-ag/entity-schema-types'

declare global {
    const Shopware: ShopwareClass;
    
    interface Window {
        Shopware: ShopwareClass;
    }

    type Entity<EntityName extends keyof EntitySchema.Entities> = EntitySchema.Entities[EntityName];
    type EntityCollection<EntityName extends keyof EntitySchema.Entities> = EntitySchema.Entities[EntityName][];
}