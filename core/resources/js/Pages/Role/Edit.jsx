import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import RoleForm from './partials/RoleForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Edit = ({ role, permissions }) => {
    const { data, setData, put, errors, processing } = useForm({
        name: role.name || '',
        permissions: role.permission_ids || [],
    });

    const handleSubmit = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }
        put(route('roles.update', role.id), { preserveScroll: true });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Edit Role"
            subtitle="Update role name and permissions"
            onSubmit={handleSubmit}
            processing={processing}
        >
            <RoleForm
                data={data}
                errors={errors}
                processing={processing}
                permissions={permissions}
                onDataChange={handleDataChange}
            >
                <FormActions
                    processing={processing}
                    submitText="Update Role"
                    cancelText="Cancel"
                    submitButtonProps={{ variant: 'primary' }}
                />
            </RoleForm>
        </FormLayout>
    );
};

export default Edit;
