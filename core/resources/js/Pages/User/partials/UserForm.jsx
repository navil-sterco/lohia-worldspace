import React from 'react';
import TextInput from '@/Components/Form/TextInput';
import InputError from '@/Components/InputError';
import { GlobeLock, Lock, Mail, Type } from 'lucide-react';

const UserForm = ({
    data,
    setData,
    errors,
    processing,
    roles = [],
    onDataChange,
    isEdit = false,
    children,
}) => {
    return (
        <>
            <div className="row">
                <div className="col-md-6">
                    <TextInput
                        name="name"
                        label="Full Name"
                        value={data.name}
                        onChange={(value) => onDataChange('name', value)}
                        error={errors.name}
                        placeholder="John Doe"
                        required={true}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                </div>
                <div className="col-md-6">
                    <TextInput
                        name="email"
                        label="Email Address"
                        type="email"
                        value={data.email}
                        onChange={(value) => onDataChange('email', value)}
                        error={errors.email}
                        placeholder="user@example.com"
                        required={true}
                        disabled={processing}
                        icon={<Mail size={16} />}
                        helperText={
                            isEdit
                                ? 'Updating email does not send a new password.'
                                : 'Login credentials will be sent to this email.'
                        }
                    />
                </div>
            </div>
            <div className="row mt-2">
                <div className="col-6">
                    <label className="form-label d-flex align-items-center gap-2">
                        <GlobeLock size={16} />
                        Role
                    </label>

                    {roles.length > 0 ? (
                        <select
                            className="form-select"
                            value={data.role || ''}
                            onChange={(e) => onDataChange('role', e.target.value)}
                            disabled={processing}
                        >
                            <option value="">Select Role</option>

                            {roles.map((role) => (
                                <option key={role.id} value={role.id}>
                                    {role.name}
                                </option>
                            ))}
                        </select>
                    ) : (
                        <p className="text-muted mb-0">
                            No roles yet.{' '}
                            <a href={route('roles.create')}>
                                Create a role
                            </a>{' '}
                            first.
                        </p>
                    )}

                    <InputError message={errors.role} className="mt-2" />
                </div>
            </div>

            {isEdit && (
                <div className="row mt-3">
                    <div className="col-md-6">
                        <TextInput
                            name="password"
                            label="New Password"
                            type="password"
                            value={data.password}
                            onChange={(value) => onDataChange('password', value)}
                            error={errors.password}
                            placeholder="Leave blank to keep current"
                            disabled={processing}
                            icon={<Lock size={16} />}
                        />
                    </div>
                    <div className="col-md-6">
                        <TextInput
                            name="password_confirmation"
                            label="Confirm Password"
                            type="password"
                            value={data.password_confirmation}
                            onChange={(value) => onDataChange('password_confirmation', value)}
                            error={errors.password_confirmation}
                            placeholder="Confirm new password"
                            disabled={processing}
                            icon={<Lock size={16} />}
                        />
                    </div>
                </div>
            )}

            {!isEdit && (
                <div className="alert alert-primary mt-2">
                    A secure password will be generated automatically and emailed to the user.
                </div>
            )}

            {children}
        </>
    );
};

export default UserForm;
