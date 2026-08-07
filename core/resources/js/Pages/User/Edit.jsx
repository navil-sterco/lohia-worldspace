import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import UserForm from './partials/UserForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Edit = ({ user, roles }) => {
    const { data, setData, put, errors, processing } = useForm({
        name: user.name || '',
        email: user.email || '',
        role: user.role_ids?.[0] || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }

        put(route('users.update', user.id), {
            preserveScroll: true,
        });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Edit User"
            subtitle="Update user details and assigned role"
            onSubmit={handleSubmit}
            processing={processing}
        >
            <UserForm
                data={data}
                errors={errors}
                processing={processing}
                roles={roles}
                onDataChange={handleDataChange}
                isEdit={true}
            >
                <FormActions
                    processing={processing}
                    submitText="Update User"
                    cancelText="Cancel"
                    submitButtonProps={{ variant: 'primary' }}
                />
            </UserForm>
        </FormLayout>
    );
};

export default Edit;