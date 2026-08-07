import React, { useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import useCtrlSSubmit from "@/hooks/useCtrlSSubmit";

export default function Create() {
    const { data, setData, post, processing, errors } = useForm({
        type: "image",
        name: "",
        file: []
    });
    
    const [alert, setAlert] = useState(null);

    const getAcceptString = () => {
        if (data.type === "file") return ".pdf";
        if (data.type === "video") return "video/*";
        return ".jpg,.jpeg,.png,.svg,.webp";
    };

    const validateFileType = (file) => {
        if (data.type === "image") {
            return file.type.startsWith("image/");
        }
        if (data.type === "file") {
            return file.type === "application/pdf" || file.name.toLowerCase().endsWith(".pdf");
        }
        if (data.type === "video") {
            return file.type.startsWith("video/");
        }
        return false;
    };

    const handleFiles = (e) => {
        const files = Array.from(e.target.files);
        const invalidFiles = files.filter(file => !validateFileType(file));

        if (invalidFiles.length > 0) {
            setAlert(`Only ${data.type === "image" ? "image" : data.type === "file" ? "PDF" : "video"} files are allowed for this type.`);
            setData("file", []);
            return;
        }

        setAlert(null);
        setData("file", files);
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (data.file.length === 0) {
            setAlert("Please select at least one file");
            return;
        }

        const formData = new FormData();
        formData.append("name", data.name);

        data.file.forEach((file) => {
            formData.append("file[]", file);
        });

        formData.append("type", data.type);

        post(route("gallery.store"), formData, {
            forceFormData: true,
            onSuccess: () => {
                setData({ type: "image", name: "", file: [] });
                const fileInput = document.querySelector('input[type="file"]');
                if (fileInput) fileInput.value = '';
            }
        });
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Upload Images / Videos / PDFs</h1>
                    <p className="text-muted mb-0">Store multiple image, video, or PDF files</p>
                </div>
            </div>

            <div className="card">
                <form onSubmit={handleSubmit}>
                    <div className="card-body">
                        <div className="row">
                            <div className="mb-3 col-md-6">
                                <label htmlFor="name" className="form-label">Name <span className="text-danger">*</span></label>
                                <input
                                    className="form-control"
                                    type="text"
                                    id="name"
                                    name="name"
                                    placeholder='About Us Image'
                                    value={data.name}
                                    onChange={(e) => setData("name",e.target.value)}
                                />
                                <div className="form-text text-danger">{errors.name}</div> 
                            </div>
                            {/* Type */}
                            <div className="mb-3 col-md-6">
                                <label className="form-label">File Type</label>
                                <select
                                    className="form-select"
                                    value={data.type}
                                    onChange={(e) => {
                                        setData("type", e.target.value);
                                        setData("images", []);
                                        setAlert(null);
                                    }}
                                >
                                    <option value="image">Image</option>
                                    <option value="file">PDF</option>
                                    <option value="video">Video</option>
                                </select>
                                {errors.type && (
                                    <div className="text-danger small">{errors.type}</div>
                                )}
                            </div>

                            {/* Files */}
                            <div className="mb-3 col-md-6">
                                <label className="form-label">Upload Files</label>
                                <input
                                    type="file"
                                    multiple
                                    className="form-control"
                                    onChange={handleFiles}
                                    accept={getAcceptString()}
                                />
                                
                                {/* Show selected files */}
                                {data.file.length > 0 && (
                                    <div className="mt-2">
                                        <small className="text-muted">
                                            Selected files: {data.file.length}
                                        </small>
                                        <ul className="small mt-1">
                                            {data.file.map((file, index) => (
                                                <li key={index}>{file.name}</li>
                                            ))}
                                        </ul>
                                    </div>
                                )}

                                {Object.keys(errors)
                                    .filter((key) => key.startsWith("file"))
                                    .map((key) => (
                                        <div key={key} className="text-danger small">
                                            {errors[key]}
                                        </div>
                                ))}                            </div>
                        </div>

                        {alert && (
                            <div className="alert alert-danger" role="alert">
                                {alert}
                            </div>
                        )}

                        <div className="mt-4 pt-3 border-top">
                            <button
                                type="submit"
                                className="btn btn-primary me-2"
                                disabled={processing || data.file.length === 0}
                            >
                                {processing ? "Uploading..." : `Upload ${data.file.length} Files`}
                            </button>

                            <Link href={route("gallery.index")} className="btn btn-secondary">
                                Cancel
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
}