import type { ShopwareClass } from 'src/core/shopware'
import '@shopware-ag/entity-schema-types'

declare global {
    const Shopware: ShopwareClass;
    
    interface Window {
        Shopware: ShopwareClass;
    }

    type Entity<EntityName extends keyof CustomEntities> = CustomEntities[EntityName];
    type EntityCollection<EntityName extends keyof CustomEntities> = CustomEntities[EntityName][];
}

interface CustomEntities extends EntitySchema.Entities {
    blur_elysium_slides: blur_elysium_slides;
}

interface blur_elysium_slides {
    id: string;
    customFields?: unknown;
    createdAt: string;
    updatedAt?: string;
}
