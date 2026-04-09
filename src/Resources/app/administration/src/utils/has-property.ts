export function hasProperty(
    obj: Record<string, unknown> | null | undefined,
    path: string
): boolean {
    let current: unknown = obj;
    for (const key of path.split('.')) {
        if (current == null || typeof current !== 'object') return false;
        if (!Object.hasOwn(current, key)) return false;
        current = (current as Record<string, unknown>)[key];
    }
    return true;
}
