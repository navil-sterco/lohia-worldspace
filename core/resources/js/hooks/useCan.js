import { usePage } from '@inertiajs/react';

export function useCan() {
    const permissions = usePage().props.auth?.permissions ?? [];

    return (permission) => permissions.includes(permission);
}

export function useModuleCan(slug) {
    const can = useCan();
    const prefix = `module.${slug}.`;

    return {
        view: can(`${prefix}entries.view`),
        create: can(`${prefix}entries.create`),
        update: can(`${prefix}entries.update`),
        delete: can(`${prefix}entries.delete`),
        mapping: can(`${prefix}entries.mapping`),
        detail: can(`${prefix}entries.detail`),
        subPages: can(`${prefix}entries.sub-pages`),
    };
}
