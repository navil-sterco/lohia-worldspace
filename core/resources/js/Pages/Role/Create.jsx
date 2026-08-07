import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import RoleForm from './partials/RoleForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Create = ({ permissions }) => {
    const { data, setData, post, errors, processing } = useForm({
        name: '',
        permissions: [],
    });

    const handleSubmit = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }
        post(route('roles.store'), { preserveScroll: true });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Create Role"
            subtitle="Define a role and assign permissions"
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
                    submitText="Create Role"
                    cancelText="Cancel"
                    submitButtonProps={{ variant: 'primary' }}
                />
            </RoleForm>
        </FormLayout>
    );
};

export default Create;
