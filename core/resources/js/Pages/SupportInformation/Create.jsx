import { useForm } from '@inertiajs/react';
import React from 'react';
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import SupportInformationForm from './partials/SupportInformationForm';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Create = () => {
    const { data, setData, post, errors, processing } = useForm({
        key: "",
        value: "",
        file: null,
    });

    const handleSubmit = (e) => {
        if (e && e.preventDefault) {
            e.preventDefault();
        }

        post(route("support-information.store"), {
            preserveScroll: true,
            forceFormData: true,
            onSuccess: () => {
                // Success message shown by flash
            },
            onError: (errors) => {
                console.log('Form errors:', errors);
            }
        });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <FormLayout
            title="Add Support Information"
            subtitle="Add new global website data like email, phone, links, and images"
            onSubmit={handleSubmit}
            processing={processing}
        >
            <SupportInformationForm
                data={data}
                errors={errors}
                processing={processing}
                onDataChange={handleDataChange}
            >
                <FormActions
                    processing={processing}
                    submitText="Create Information"
                    cancelText="Cancel"
                    submitButtonProps={{
                        variant: 'primary',
                    }}
                />
            </SupportInformationForm>
        </FormLayout>
    );
};

export default Create;
