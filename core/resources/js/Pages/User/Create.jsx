import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import UserForm from './partials/UserForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Create = ({ roles }) => {
    const { data, setData, post, errors, processing } = useForm({
        name: '',
        email: '',
        role: '',
    });

    const handleSubmit = (e) => {
        if (e?.preventDefault) {
            e.preventDefault();
        }

        post(route('users.store'), {
            preserveScroll: true,
        });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Create User"
            subtitle="Add a new admin user and send login credentials by email"
            onSubmit={handleSubmit}
            processing={processing}
        >
            <UserForm
                data={data}
                errors={errors}
                processing={processing}
                roles={roles}
                onDataChange={handleDataChange}
            >
                <FormActions
                    processing={processing}
                    submitText="Create User"
                    cancelText="Cancel"
                    submitButtonProps={{ variant: 'primary' }}
                />
            </UserForm>
        </FormLayout>
    );
};

export default Create;