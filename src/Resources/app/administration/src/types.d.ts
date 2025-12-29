import type { ShopwareClass } from 'src/core/shopware'
import type EntityCollectionClass from 'src/core/data/entity-collection.data'
import '@shopware-ag/entity-schema-types'

declare global {
    const Shopware: ShopwareClass;
    
    interface Window {
        Shopware: ShopwareClass;
    }

    type Entity<EntityName extends keyof EntitySchema.Entities> = EntitySchema.Entities[EntityName];
    type EntityCollection<EntityName extends keyof EntitySchema.Entities> = EntityCollectionClass<EntityName>;
}

declare global {
    namespace EntitySchema {
        interface Entities {
            blur_elysium_slides: blur_elysium_slides;
        }
    }
}

interface blur_elysium_slides {
    id: string;
    customFields?: unknown;
    createdAt: string;
    updatedAt?: string;
}
