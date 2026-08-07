import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import PermissionForm from './partials/PermissionForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Edit = ({ permission }) => {
    const { data, setData, put, errors, processing } = useForm({
        name: permission.name || '',
    });

    const handleSubmit = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }
        put(route('permissions.update', permission.id), { preserveScroll: true });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Edit Permission"
            subtitle="Update permission name"
            onSubmit={handleSubmit}
            processing={processing}
        >
            <PermissionForm
                data={data}
                errors={errors}
                processing={processing}
                onDataChange={handleDataChange}
            >
                <FormActions
                    processing={processing}
                    submitText="Update Permission"
                    cancelText="Cancel"
                    submitButtonProps={{ variant: 'primary' }}
                />
            </PermissionForm>
        </FormLayout>
    );
};

export default Edit;
