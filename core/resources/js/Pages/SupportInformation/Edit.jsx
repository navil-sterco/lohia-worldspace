import { useForm } from "@inertiajs/react";
import FormLayout from '@/Components/FormLayout';
import FormActions from '@/Components/FormActions';
import SupportInformationForm from "./partials/SupportInformationForm";
import useCtrlSSubmit from "@/hooks/useCtrlSSubmit";

const Edit = ({ supportInformation }) => {
    const { data, setData, post, errors, processing } = useForm({
        _method: "PUT",
        key: supportInformation.key || "",
        value: supportInformation.value || "",
        file: null,
        currentImage: supportInformation.file_path || null,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route("support-information.update", supportInformation.id), {
            forceFormData: true,
        });
    };

    useCtrlSSubmit(handleSubmit, !processing);

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    return (
        <FormLayout
            title="Edit Support Information"
            subtitle="Update your global website data"
            onSubmit={handleSubmit}
            processing={processing}
        >
            <SupportInformationForm
                data={data}
                errors={errors}
                processing={processing}
                onDataChange={handleDataChange}
                isEdit={true}
            >
                <FormActions
                    processing={processing}
                    submitText="Update Information"
                    cancelText="Cancel"
                />
            </SupportInformationForm>
        </FormLayout>
    );
};

export default Edit;
