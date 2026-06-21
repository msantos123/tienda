import { route as ziggyRoute } from '../../vendor/tightenco/ziggy';

/**
 * Custom route() helper that automatically includes the tenant parameter
 * when a tenant is active.
 */
export function route(name, params, absolute, config) {
    const tenant = window.Laravel?.tenant;
    
    // If there is an active tenant, add it to the parameters
    if (tenant) {
        if (typeof params === 'undefined' || params === null) {
            // No parameters: create object with tenant
            params = { tenant: tenant };
        } else if (Array.isArray(params)) {
            // If it's an array, convert to object with tenant or prepend
            params = { tenant: tenant, ...params };
        } else if (typeof params === 'object') {
            // If it's an object, add tenant if it doesn't exist
            if (!params.tenant) {
                params = { tenant: tenant, ...params };
            }
        } else {
            // If it's a primitive (ID), assume it's the second parameter (e.g., 'id')
            params = { tenant: tenant, id: params };
        }
    }
    
    return ziggyRoute(name, params, absolute, config);
}

export default route;
