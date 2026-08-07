import React from 'react';
import TextInput from '@/Components/Form/TextInput';
import { Key } from 'lucide-react';

const PermissionForm = ({ data, errors, processing, onDataChange, children }) => {
    return (
        <>
            <div className="row">
                <div className="col-md-6">
                    <TextInput
                        name="name"
                        label="Permission Name"
                        value={data.name}
                        onChange={(value) => onDataChange('name', value)}
                        error={errors.name}
                        placeholder="e.g. pages.create"
                        required={true}
                        disabled={processing}
                        icon={<Key size={16} />}
                        helperText="Use dot notation: resource.action (e.g. users.edit)."
                    />
                </div>
            </div>
            {children}
        </>
    );
};

export default PermissionForm;
