import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import PermissionForm from './partials/PermissionForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Create = () => {
    const { data, setData, post, errors, processing } = useForm({
        name: '',
    });

    const handleSubmit = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }
        post(route('permissions.store'), { preserveScroll: true });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Create Permission"
            subtitle="Add a new permission for role assignment"
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
                    submitText="Create Permission"
                    cancelText="Cancel"
                    submitButtonProps={{ variant: 'primary' }}
                />
            </PermissionForm>
        </FormLayout>
    );
};

export default Create;
