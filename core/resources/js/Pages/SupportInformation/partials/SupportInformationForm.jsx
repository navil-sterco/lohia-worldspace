import React, { useState } from 'react';
import TextInput from '@/Components/Form/TextInput';
import { Type, FileImage } from 'lucide-react';

const SupportInformationForm = ({ data, errors, processing, onDataChange, isEdit, children }) => {
    const [imagePreview, setImagePreview] = useState(isEdit ? data.currentImage : null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            onDataChange('file', file);
            
            // Create preview
            const reader = new FileReader();
            reader.onloadend = () => {
                setImagePreview(reader.result);
            };
            reader.readAsDataURL(file);
        }
    };

    return (
        <>
            <div className="row">
                <div className="col-md-6">
                    <TextInput
                        name="key"
                        label="Key"
                        value={data.key}
                        onChange={(value) => onDataChange("key", value)}
                        error={errors.key}
                        placeholder="e.g., support_email, apply_now_link"
                        required={true}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                    <div className="form-text">
                        A unique identifier. Use lowercase with underscores (e.g., support_phone, office_address)
                    </div>
                </div>

                <div className="col-md-6">
                    <label className="form-label">Image (Optional)</label>
                    <div className="input-group">
                        <input
                            type="file"
                            className="form-control"
                            accept="image/*"
                            onChange={handleFileChange}
                            disabled={processing}
                        />
                        <span className="input-group-text">
                            <FileImage size={16} />
                        </span>
                    </div>
                    {errors.file && <div className="text-danger small">{errors.file}</div>}
                    <div className="form-text">
                        Accepted: JPG, PNG, SVG, WebP. Max size: 2MB
                    </div>
                </div>
            </div>

            {imagePreview && (
                <div className="row mt-3">
                    <div className="col-md-6">
                        <div className="card">
                            <div className="card-body">
                                <p className="card-text mb-2">Image Preview:</p>
                                <img
                                    src={imagePreview}
                                    alt="preview"
                                    style={{ maxWidth: '100%', maxHeight: '200px', borderRadius: '4px' }}
                                />
                            </div>
                        </div>
                    </div>
                </div>
            )}

            <div className="row mt-3">
                <div className="col-12">
                    <label className="form-label">Value</label>
                    <textarea
                        className="form-control"
                        rows="5"
                        value={data.value}
                        onChange={(e) => onDataChange('value', e.target.value)}
                        placeholder="Enter email, phone, URL, address or other content..."
                        disabled={processing}
                    />
                    {errors.value && <div className="text-danger small">{errors.value}</div>}
                    <div className="form-text">
                        Store email addresses, phone numbers, URLs, addresses, or any global data
                    </div>
                </div>
            </div>

            {children}
        </>
    );
};

export default SupportInformationForm;
