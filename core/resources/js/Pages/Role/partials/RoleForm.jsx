import React from 'react';
import TextInput from '@/Components/Form/TextInput';
import InputError from '@/Components/InputError';
import { GlobeLock, Key } from 'lucide-react';

const RoleForm = ({ data, errors, processing, permissions = [], onDataChange, children }) => {
    const togglePermission = (permissionId) => {
        const selected = data.permissions || [];
        const next = selected.includes(permissionId)
            ? selected.filter((id) => id !== permissionId)
            : [...selected, permissionId];
        onDataChange('permissions', next);
    };

    const toggleGroup = (groupPermissions) => {
        const ids = groupPermissions.map((p) => p.id);
        const selected = data.permissions || [];
        const allSelected = ids.every((id) => selected.includes(id));
        const next = allSelected
            ? selected.filter((id) => !ids.includes(id))
            : [...new Set([...selected, ...ids])];
        onDataChange('permissions', next);
    };

    const isGroupChecked = (groupPermissions) =>
        groupPermissions.length > 0 &&
        groupPermissions.every((p) => (data.permissions || []).includes(p.id));

    const hasGroups = permissions.length > 0 && permissions[0]?.permissions;

    return (
        <>
            <div className="row">
                <div className="col-md-6">
                    <TextInput
                        name="name"
                        label="Role Name"
                        value={data.name}
                        onChange={(value) => onDataChange('name', value)}
                        error={errors.name}
                        placeholder="e.g. content-manager"
                        required={true}
                        disabled={processing}
                        icon={<GlobeLock size={16} />}
                        helperText="Use lowercase with hyphens (e.g. admin, content-editor)."
                    />
                </div>
            </div>

            <div className="mt-3">
                <label className="form-label d-flex align-items-center gap-2">
                    <Key size={16} />
                    Permissions
                </label>

                {hasGroups ? (
                    <div className="d-flex flex-column gap-3">
                        {permissions.map((group) => (
                            <div key={group.label} className="border rounded p-3">
                                <div className="d-flex align-items-center justify-content-between mb-2">
                                    <strong className="text-capitalize">{group.label}</strong>
                                    <button
                                        type="button"
                                        className="btn btn-sm btn-outline-secondary"
                                        onClick={() => toggleGroup(group.permissions)}
                                        disabled={processing || group.permissions.length === 0}
                                    >
                                        {isGroupChecked(group.permissions) ? 'Deselect all' : 'Select all'}
                                    </button>
                                </div>
                                <div className="row g-2">
                                    {group.permissions.map((permission) => (
                                        <div key={permission.id} className="col-md-4 col-sm-6">
                                            <div className="form-check">
                                                <input
                                                    className="form-check-input"
                                                    type="checkbox"
                                                    id={`permission-${permission.id}`}
                                                    checked={(data.permissions || []).includes(permission.id)}
                                                    onChange={() => togglePermission(permission.id)}
                                                    disabled={processing}
                                                />
                                                <label
                                                    className="form-check-label"
                                                    htmlFor={`permission-${permission.id}`}
                                                    title={permission.name}
                                                >
                                                    {permission.label}
                                                </label>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        ))}
                    </div>
                ) : permissions.length > 0 ? (
                    <div className="row g-2">
                        {permissions.map((permission) => (
                            <div key={permission.id} className="col-md-4 col-sm-6">
                                <div className="form-check">
                                    <input
                                        className="form-check-input"
                                        type="checkbox"
                                        id={`permission-${permission.id}`}
                                        checked={(data.permissions || []).includes(permission.id)}
                                        onChange={() => togglePermission(permission.id)}
                                        disabled={processing}
                                    />
                                    <label className="form-check-label" htmlFor={`permission-${permission.id}`}>
                                        {permission.name}
                                    </label>
                                </div>
                            </div>
                        ))}
                    </div>
                ) : (
                    <p className="text-muted mb-0">
                        No permissions yet. Run{' '}
                        <code>php artisan modules:sync-permissions</code> or create modules to generate them.
                    </p>
                )}

                <InputError message={errors.permissions} className="mt-2" />
            </div>

            {children}
        </>
    );
};

export default RoleForm;
