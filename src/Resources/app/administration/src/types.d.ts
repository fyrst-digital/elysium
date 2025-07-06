import type { ShopwareClass } from 'src/core/shopware';

declare global {
    const Shopware: ShopwareClass;
    
    interface Window {
        Shopware: ShopwareClass;
    }
}