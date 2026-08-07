import React, { useEffect, useState } from "react";
import { router, usePage } from "@inertiajs/react";
import CodeEditor from "@/Components/Fields/CodeEditor";
import { ToastContainer, toast } from "react-toastify";

const FIELD_TYPES = [
    { value: "text", label: "Text Input" },
    { value: "textarea", label: "Text Area" },
    { value: "code", label: "Code Editor" },
    { value: "number", label: "Number" },
    { value: "email", label: "Email" },
    { value: "url", label: "URL" },
    { value: "select", label: "Dropdown Select" },
    { value: "checkbox", label: "Checkbox" },
    { value: "radio", label: "Radio Button" },
    { value: "file", label: "File Upload" },
    { value: "image", label: "Image Upload" },
    { value: "date", label: "Date" },
    { value: "color", label: "Color Picker" },
    { value: "repeater", label: "Repeater" },
];

const CHILD_FIELD_TYPES = FIELD_TYPES.filter(
    (type) => type.value !== "repeater",
);

const FULL_WIDTH_TYPES = ["textarea", "code", "repeater"];
const isFullWidthField = (type) => FULL_WIDTH_TYPES.includes(type);

const emptyField = () => ({
    name: "",
    label: "",
    type: "text",
    placeholder: "",
    options: "",
    fields: [],
    value: "",
});

const emptyChildField = () => ({
    name: "",
    label: "",
    type: "text",
    placeholder: "",
    options: "",
    value: "",
});

const cleanName = (value, fallback = "field") => {
    const name = String(value || "")
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "_")
        .replace(/_+/g, "_")
        .replace(/^_+|_+$/g, "");

    return /^[a-z]/.test(name) ? name : fallback;
};

const optionsToText = (options) =>
    Array.isArray(options) ? options.join(", ") : options || "";

const optionsToArray = (options) =>
    Array.isArray(options)
        ? options
        : String(options || "")
              .split(",")
              .map((option) => option.trim())
              .filter(Boolean);

const normalizeFieldForUi = (field) => ({
    ...emptyField(),
    ...field,
    options: optionsToText(field.options),
    fields: (field.fields || []).map((child) => ({
        ...emptyChildField(),
        ...child,
        options: optionsToText(child.options),
        value: "",
    })),
    value:
        field.value ??
        (field.type === "checkbox"
            ? false
            : field.type === "repeater"
              ? []
              : ""),
});

const prepareField = (field) => ({
    ...field,
    options: optionsToArray(field.options),
    fields: (field.fields || []).map((child) => ({
        ...child,
        options: optionsToArray(child.options),
        value: "",
    })),
    value:
        field.type === "checkbox"
            ? !!field.value
            : field.type === "repeater"
              ? Array.isArray(field.value)
                  ? field.value
                  : []
              : (field.value ?? ""),
});

const sectionFromServer = (section, index) => ({
    key: section.key || `section_${Date.now()}_${index}`,
    title: section.title || `Section ${index + 1}`,
    is_active: section.is_active ?? true,
    fields: (section.fields || []).map(normalizeFieldForUi),
});

export default function Edit({ homePage }) {
    const [title, setTitle] = useState(homePage?.title || "Home Page");
    const [isActive, setIsActive] = useState(homePage?.is_active ?? true);
    const [sections, setSections] = useState(() =>
        (homePage?.sections || []).map(sectionFromServer),
    );
    const [fieldFiles, setFieldFiles] = useState({});
    const [processing, setProcessing] = useState(false);
    const [errors, setErrors] = useState({});
    const [modal, setModal] = useState(null);
    const [expandedSections, setExpandedSections] = useState({});
    const { flash } = usePage().props;

    useEffect(() => {
        if (flash.success) toast.success(flash.success);
    }, [flash]);

    const toggleSection = (key) => {
        setExpandedSections((current) => ({
            ...current,
            [key]: !current[key],
        }));
    };

    const stopToggle = (event) => {
        event.stopPropagation();
    };

    const updateSection = (sectionIndex, patchOrUpdater) => {
        setSections((current) =>
            current.map((section, index) => {
                if (index !== sectionIndex) return section;
                return typeof patchOrUpdater === "function"
                    ? patchOrUpdater(section)
                    : { ...section, ...patchOrUpdater };
            }),
        );
    };

    const addSection = () => {
        const key = `section_${Date.now()}`;
        setSections((current) => [
            ...current,
            {
                key,
                title: `Section ${current.length + 1}`,
                is_active: true,
                fields: [],
            },
        ]);
        setExpandedSections((current) => ({ ...current, [key]: true }));
    };

    const moveSection = (sectionIndex, direction) => {
        setSections((current) => {
            const targetIndex = sectionIndex + direction;
            if (targetIndex < 0 || targetIndex >= current.length) {
                return current;
            }
            const next = [...current];
            [next[sectionIndex], next[targetIndex]] = [
                next[targetIndex],
                next[sectionIndex],
            ];
            return next;
        });
    };

    const removeSection = (sectionIndex) => {
        setSections((current) =>
            current.filter((_, index) => index !== sectionIndex),
        );
    };

    const openFieldModal = (sectionIndex, fieldIndex = null) => {
        const field =
            fieldIndex === null
                ? emptyField()
                : sections[sectionIndex].fields[fieldIndex];
        setModal({
            sectionIndex,
            fieldIndex,
            draft: normalizeFieldForUi(field),
            childDraft: emptyChildField(),
        });
    };

    const saveModalField = () => {
        if (!modal) return;
        const label = String(modal.draft.label || "").trim();
        if (!label) return;

        updateSection(modal.sectionIndex, (section) => {
            const existingNames = new Set(
                section.fields
                    .map((field, index) =>
                        index === modal.fieldIndex ? null : field.name,
                    )
                    .filter(Boolean),
            );
            const fallback = `field_${section.fields.length + 1}`;
            const baseName = cleanName(modal.draft.name || label, fallback);
            let name = baseName;
            let counter = 2;

            while (existingNames.has(name)) {
                name = `${baseName}_${counter}`;
                counter += 1;
            }

            const field = {
                ...modal.draft,
                name,
                label,
                value:
                    modal.draft.type === "checkbox"
                        ? !!modal.draft.value
                        : modal.draft.type === "repeater"
                          ? Array.isArray(modal.draft.value)
                              ? modal.draft.value
                              : []
                          : (modal.draft.value ?? ""),
            };

            if (modal.fieldIndex === null) {
                return { ...section, fields: [...section.fields, field] };
            }

            return {
                ...section,
                fields: section.fields.map((item, index) =>
                    index === modal.fieldIndex ? field : item,
                ),
            };
        });

        setModal(null);
    };

    const updateModalDraft = (patch) => {
        setModal((current) =>
            current
                ? { ...current, draft: { ...current.draft, ...patch } }
                : current,
        );
    };

    const updateChildDraft = (patch) => {
        setModal((current) =>
            current
                ? {
                      ...current,
                      childDraft: { ...current.childDraft, ...patch },
                  }
                : current,
        );
    };

    const addChildField = () => {
        if (!modal) return;
        const label = String(modal.childDraft.label || "").trim();
        if (!label) return;

        const existingNames = new Set(
            (modal.draft.fields || []).map((field) => field.name),
        );
        const fallback = `item_field_${(modal.draft.fields || []).length + 1}`;
        const baseName = cleanName(modal.childDraft.name || label, fallback);
        let name = baseName;
        let counter = 2;

        while (existingNames.has(name)) {
            name = `${baseName}_${counter}`;
            counter += 1;
        }

        updateModalDraft({
            fields: [
                ...(modal.draft.fields || []),
                { ...modal.childDraft, name, label },
            ],
        });
        updateChildDraft(emptyChildField());
    };

    const removeChildField = (childIndex) => {
        updateModalDraft({
            fields: (modal?.draft.fields || []).filter(
                (_, index) => index !== childIndex,
            ),
        });
    };

    const updateField = (sectionIndex, fieldIndex, patch) => {
        updateSection(sectionIndex, (section) => ({
            ...section,
            fields: section.fields.map((field, index) =>
                index === fieldIndex ? { ...field, ...patch } : field,
            ),
        }));
    };

    const removeField = (sectionIndex, fieldIndex) => {
        updateSection(sectionIndex, (section) => ({
            ...section,
            fields: section.fields.filter((_, index) => index !== fieldIndex),
        }));
    };

    const handleFileChange = (sectionIndex, fieldIndex, file) => {
        updateField(sectionIndex, fieldIndex, { value: file });
    };

    const handleRepeaterFileChange = (
        sectionIndex,
        fieldIndex,
        rowIndex,
        childName,
        file,
    ) => {
        updateRepeaterRow(sectionIndex, fieldIndex, rowIndex, childName, file);
    };

    const updateRepeaterRow = (
        sectionIndex,
        fieldIndex,
        rowIndex,
        childName,
        value,
    ) => {
        const field = sections[sectionIndex].fields[fieldIndex];
        const rows = Array.isArray(field.value) ? [...field.value] : [];
        rows[rowIndex] = { ...(rows[rowIndex] || {}), [childName]: value };
        updateField(sectionIndex, fieldIndex, { value: rows });
    };

    const addRepeaterRow = (sectionIndex, fieldIndex) => {
        const field = sections[sectionIndex].fields[fieldIndex];
        const row = {};
        (field.fields || []).forEach((child) => {
            row[child.name] = child.type === "checkbox" ? false : "";
        });
        updateField(sectionIndex, fieldIndex, {
            value: [...(Array.isArray(field.value) ? field.value : []), row],
        });
    };

    const removeRepeaterRow = (sectionIndex, fieldIndex, rowIndex) => {
        const field = sections[sectionIndex].fields[fieldIndex];
        updateField(sectionIndex, fieldIndex, {
            value: (Array.isArray(field.value) ? field.value : []).filter(
                (_, index) => index !== rowIndex,
            ),
        });
    };

    const submit = (event) => {
        event.preventDefault();
        setProcessing(true);
        setErrors({});

        const payloadSections = sections
            .map((section) => ({
                key: section.key,
                title: section.title,
                is_active: section.is_active,
                fields: section.fields
                    .filter((field) => String(field.label || "").trim() !== "")
                    .map(prepareField),
            }))
            .filter((section) => section.fields.length > 0);

        const formData = new FormData();
        formData.append("title", title);
        formData.append("is_active", isActive ? "1" : "0");
        formData.append("sections_json", JSON.stringify(payloadSections));

        // Find and append files directly from sections state
        sections.forEach((section, sectionIndex) => {
            section.fields.forEach((field, fieldIndex) => {
                if (field.type === "repeater") {
                    if (Array.isArray(field.value)) {
                        field.value.forEach((row, rowIndex) => {
                            (field.fields || []).forEach((child) => {
                                if (
                                    child.type === "file" ||
                                    child.type === "image"
                                ) {
                                    const val = row[child.name];
                                    if (val instanceof File) {
                                        formData.append(
                                            `repeater_files[${sectionIndex}][${fieldIndex}][${rowIndex}][${child.name}]`,
                                            val,
                                        );
                                    }
                                }
                            });
                        });
                    }
                } else if (field.type === "file" || field.type === "image") {
                    const val = field.value;
                    if (val instanceof File) {
                        formData.append(
                            `field_files[${sectionIndex}][${fieldIndex}]`,
                            val,
                        );
                    }
                }
            });
        });

        router.post(route("home-page.update"), formData, {
            forceFormData: true,
            preserveScroll: true,
            onError: setErrors,
            onFinish: () => setProcessing(false),
        });
    };

    const renderSimpleInput = (field, value, onChange, fileHandler = null) => {
        if (field.type === "textarea") {
            return (
                <textarea
                    className="form-control"
                    rows="4"
                    value={value ?? ""}
                    placeholder={field.placeholder || ""}
                    onChange={(event) => onChange(event.target.value)}
                />
            );
        }

        if (field.type === "code") {
            return (
                <CodeEditor
                    value={value ?? ""}
                    onChange={onChange}
                    height="180px"
                />
            );
        }

        if (field.type === "select") {
            const options = optionsToArray(field.options);
            return (
                <select
                    className="form-select"
                    value={value ?? ""}
                    onChange={(event) => onChange(event.target.value)}
                >
                    <option value="">Select</option>
                    {options.map((option) => (
                        <option key={option} value={option}>
                            {option}
                        </option>
                    ))}
                </select>
            );
        }

        if (field.type === "radio") {
            const options = optionsToArray(field.options);
            return (
                <div className="d-flex flex-wrap gap-3">
                    {options.map((option) => (
                        <label key={option} className="form-check mb-0">
                            <input
                                type="radio"
                                className="form-check-input"
                                checked={value === option}
                                onChange={() => onChange(option)}
                            />
                            <span className="form-check-label">{option}</span>
                        </label>
                    ))}
                </div>
            );
        }

        if (field.type === "checkbox") {
            return (
                <div className="form-check form-switch">
                    <input
                        type="checkbox"
                        className="form-check-input"
                        checked={!!value}
                        onChange={(event) => onChange(event.target.checked)}
                    />
                </div>
            );
        }

        if (field.type === "file" || field.type === "image") {
            return (
                <>
                    <input
                        type="file"
                        className="form-control"
                        accept={field.type === "image" ? "image/*" : undefined}
                        onChange={(event) =>
                            fileHandler
                                ? fileHandler(event.target.files?.[0] || null)
                                : onChange(event.target.files?.[0] || "")
                        }
                    />
                    {typeof value === "string" && value.startsWith("http") && (
                        <a
                            className="small d-inline-block mt-1"
                            href={value}
                            target="_blank"
                            rel="noreferrer"
                        >
                            Current file
                        </a>
                    )}
                    {value instanceof File && (
                        <div className="small text-muted mt-1">
                            New file selected: {value.name}
                        </div>
                    )}
                </>
            );
        }

        return (
            <input
                className="form-control"
                type={field.type === "text" ? "text" : field.type}
                value={value ?? ""}
                placeholder={field.placeholder || ""}
                onChange={(event) => onChange(event.target.value)}
            />
        );
    };

    const renderFieldValue = (field, sectionIndex, fieldIndex) => {
        if (field.type === "repeater") {
            const rows = Array.isArray(field.value) ? field.value : [];
            return (
                <div className="p-3 bg-light bg-opacity-50">
                    {rows.length === 0 && (
                        <div className="text-muted small mb-3">
                            No repeater items yet.
                        </div>
                    )}
                    {rows.map((row, rowIndex) => (
                        <div
                            className="border rounded p-3 mb-3 bg-white"
                            key={rowIndex}
                        >
                            <div className="d-flex justify-content-between align-items-center mb-3">
                                <strong>Item {rowIndex + 1}</strong>
                                <button
                                    type="button"
                                    className="btn btn-sm btn-outline-danger"
                                    onClick={() =>
                                        removeRepeaterRow(
                                            sectionIndex,
                                            fieldIndex,
                                            rowIndex,
                                        )
                                    }
                                >
                                    <i className="bx bx-trash"></i>
                                </button>
                            </div>
                            <div className="row g-3">
                                {(field.fields || []).map((child) => (
                                    <div
                                        className={
                                            child.type === "code" ||
                                            child.type === "textarea"
                                                ? "col-md-12"
                                                : "col-md-6"
                                        }
                                        key={child.name}
                                    >
                                        <label className="form-label">
                                            {child.label}
                                        </label>
                                        {renderSimpleInput(
                                            child,
                                            row[child.name],
                                            (value) =>
                                                updateRepeaterRow(
                                                    sectionIndex,
                                                    fieldIndex,
                                                    rowIndex,
                                                    child.name,
                                                    value,
                                                ),
                                            child.type === "file" ||
                                                child.type === "image"
                                                ? (file) =>
                                                      handleRepeaterFileChange(
                                                          sectionIndex,
                                                          fieldIndex,
                                                          rowIndex,
                                                          child.name,
                                                          file,
                                                      )
                                                : null,
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    ))}
                    <button
                        type="button"
                        className="btn btn-sm btn-outline-primary"
                        onClick={() => addRepeaterRow(sectionIndex, fieldIndex)}
                    >
                        <i className="bx bx-plus me-1"></i>
                        Add Item
                    </button>
                </div>
            );
        }

        return renderSimpleInput(
            field,
            field.value,
            (value) => updateField(sectionIndex, fieldIndex, { value }),
            (file) => handleFileChange(sectionIndex, fieldIndex, file),
        );
    };

    return (
        <form onSubmit={submit}>
            <ToastContainer />
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Home Page</h1>
                    <p className="text-muted mb-0">
                        Manage homepage sections and their content.
                    </p>
                </div>
                <button
                    type="submit"
                    className="btn btn-primary"
                    disabled={processing}
                >
                    <i className="bx bx-save me-2"></i>
                    {processing ? "Saving..." : "Save Home Page"}
                </button>
            </div>

            <div
                className="card mb-4"
                style={{ borderRadius: "0.75rem", overflow: "hidden" }}
            >
                <div className="card-body">
                    <div className="row g-3 align-items-end">
                        <div className="col-md-8">
                            <label className="form-label">Page Title</label>
                            <input
                                className="form-control"
                                value={title}
                                onChange={(event) =>
                                    setTitle(event.target.value)
                                }
                            />
                            {errors.title && (
                                <div className="text-danger small mt-1">
                                    {errors.title}
                                </div>
                            )}
                        </div>
                        <div className="col-md-4">
                            <div className="form-check form-switch">
                                <input
                                    type="checkbox"
                                    className="form-check-input"
                                    checked={isActive}
                                    onChange={(event) =>
                                        setIsActive(event.target.checked)
                                    }
                                />
                                <label className="form-check-label">
                                    Active Home Page
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {sections.length === 0 && (
                <div
                    className="card mb-4"
                    style={{ borderRadius: "0.75rem", overflow: "hidden" }}
                >
                    <div className="card-body text-center py-5">
                        <h5 className="mb-2">No sections yet</h5>
                        <p className="text-muted mb-3">
                            Create a section, then add fields inside it.
                        </p>
                        <button
                            type="button"
                            className="btn btn-primary"
                            onClick={addSection}
                        >
                            <i className="bx bx-plus me-2"></i>
                            Add Section
                        </button>
                    </div>
                </div>
            )}

            {sections.map((section, sectionIndex) => {
                const isExpanded = !!expandedSections[section.key];

                return (
                    <div
                        className="card mb-4 border"
                        style={{ borderRadius: "0.75rem", overflow: "hidden" }}
                        key={section.key}
                    >
                        <div
                            className="card-header"
                            role="button"
                            onClick={() => toggleSection(section.key)}
                            style={{
                                cursor: "pointer",
                                borderRadius: isExpanded
                                    ? "0.75rem 0.75rem 0 0"
                                    : "0.75rem",
                            }}
                        >
                            <div className="d-flex flex-wrap align-items-center gap-2">
                                <i
                                    className="bx bx-chevron-down flex-shrink-0"
                                    style={{
                                        fontSize: "1.25rem",
                                        transition: "transform 0.2s ease",
                                        transform: isExpanded
                                            ? "rotate(0deg)"
                                            : "rotate(-90deg)",
                                    }}
                                ></i>
                                <span className="badge bg-label-primary">
                                    #{sectionIndex + 1}
                                </span>
                                <input
                                    className="form-control form-control-sm"
                                    style={{ width: "200px" }}
                                    value={section.title}
                                    onClick={stopToggle}
                                    onChange={(event) =>
                                        updateSection(sectionIndex, {
                                            title: event.target.value,
                                        })
                                    }
                                    placeholder="Section title"
                                />
                                <div
                                    className="form-check form-switch mb-0"
                                    onClick={stopToggle}
                                >
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={section.is_active}
                                        onChange={(event) =>
                                            updateSection(sectionIndex, {
                                                is_active: event.target.checked,
                                            })
                                        }
                                        id={`sectionActive_${sectionIndex}`}
                                    />
                                    <label
                                        className="form-check-label small text-muted"
                                        htmlFor={`sectionActive_${sectionIndex}`}
                                    >
                                        Active
                                    </label>
                                </div>

                                <div
                                    className="ms-auto d-flex align-items-center gap-2"
                                    onClick={stopToggle}
                                >
                                    <div
                                        className="btn-group btn-group-sm"
                                        role="group"
                                    >
                                        <button
                                            type="button"
                                            className="btn btn-outline-secondary"
                                            onClick={() =>
                                                moveSection(sectionIndex, -1)
                                            }
                                            disabled={sectionIndex === 0}
                                            title="Move up"
                                        >
                                            <i className="bx bx-chevron-up"></i>
                                        </button>
                                        <button
                                            type="button"
                                            className="btn btn-outline-secondary"
                                            onClick={() =>
                                                moveSection(sectionIndex, 1)
                                            }
                                            disabled={
                                                sectionIndex ===
                                                sections.length - 1
                                            }
                                            title="Move down"
                                        >
                                            <i className="bx bx-chevron-down"></i>
                                        </button>
                                    </div>
                                    <button
                                        type="button"
                                        className="btn btn-sm btn-primary"
                                        onClick={() =>
                                            openFieldModal(sectionIndex)
                                        }
                                    >
                                        <i className="bx bx-plus me-1"></i>
                                        Add Field
                                    </button>

                                    <button
                                        type="button"
                                        className="btn btn-sm btn-outline-danger"
                                        onClick={() =>
                                            removeSection(sectionIndex)
                                        }
                                        title="Delete"
                                    >
                                        <i className="bx bx-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {isExpanded && (
                            <div className="card-body">
                                {section.fields.length === 0 ? (
                                    <div className="alert alert-info mb-0">
                                        No fields in this section. Click Add
                                        Field to create one.
                                    </div>
                                ) : (
                                    <div className="d-flex flex-wrap gap-3">
                                        {section.fields.map(
                                            (field, fieldIndex) => (
                                                <div
                                                    className="border rounded-3 p-3"
                                                    style={{
                                                        flex: isFullWidthField(
                                                            field.type,
                                                        )
                                                            ? "1 1 100%"
                                                            : "1 1 300px",
                                                    }}
                                                    key={`${field.name}_${fieldIndex}`}
                                                >
                                                    <div className="d-flex justify-content-between align-items-start gap-2 mb-2">
                                                        <div>
                                                            <div className="fw-medium">
                                                                {field.label}
                                                            </div>
                                                        </div>

                                                        <div className="d-flex align-items-center gap-2 flex-shrink-0">
                                                            <button
                                                                type="button"
                                                                className="btn btn-sm btn-outline-primary"
                                                                onClick={() =>
                                                                    openFieldModal(
                                                                        sectionIndex,
                                                                        fieldIndex,
                                                                    )
                                                                }
                                                                title="Edit field"
                                                            >
                                                                <i className="bx bx-edit"></i>
                                                            </button>
                                                            <button
                                                                type="button"
                                                                className="btn btn-sm btn-outline-danger"
                                                                onClick={() =>
                                                                    removeField(
                                                                        sectionIndex,
                                                                        fieldIndex,
                                                                    )
                                                                }
                                                                title="Delete field"
                                                            >
                                                                <i className="bx bx-trash"></i>
                                                            </button>
                                                        </div>
                                                    </div>

                                                    {renderFieldValue(
                                                        field,
                                                        sectionIndex,
                                                        fieldIndex,
                                                    )}
                                                </div>
                                            ),
                                        )}
                                    </div>
                                )}
                            </div>
                        )}
                    </div>
                );
            })}

            {sections.length > 0 && (
                <div className="d-flex justify-content-between mb-5">
                    <button
                        type="button"
                        className="btn btn-outline-primary"
                        onClick={addSection}
                    >
                        <i className="bx bx-plus me-2"></i>
                        Add Section
                    </button>
                    <button
                        type="submit"
                        className="btn btn-primary px-4"
                        disabled={processing}
                    >
                        <i className="bx bx-save me-2"></i>
                        {processing ? "Saving..." : "Save Home Page"}
                    </button>
                </div>
            )}

            {modal && (
                <div
                    className="modal fade show d-block"
                    tabIndex="-1"
                    style={{ background: "rgba(0,0,0,0.45)" }}
                >
                    <div className="modal-dialog modal-xl modal-dialog-scrollable">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">
                                    {modal.fieldIndex === null
                                        ? "Add Field"
                                        : "Edit Field"}
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={() => setModal(null)}
                                ></button>
                            </div>
                            <div className="modal-body">
                                <div className="row g-3">
                                    <div className="col-md-3">
                                        <label className="form-label">
                                            Label
                                        </label>
                                        <input
                                            className="form-control"
                                            value={modal.draft.label}
                                            onChange={(event) =>
                                                updateModalDraft({
                                                    label: event.target.value,
                                                    name: cleanName(
                                                        event.target.value,
                                                        modal.draft.name ||
                                                            "field",
                                                    ),
                                                })
                                            }
                                        />
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">
                                            Name
                                        </label>
                                        <input
                                            className="form-control"
                                            value={modal.draft.name}
                                            onChange={(event) =>
                                                updateModalDraft({
                                                    name: event.target.value,
                                                })
                                            }
                                        />
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">
                                            Type
                                        </label>
                                        <select
                                            className="form-select"
                                            value={modal.draft.type}
                                            onChange={(event) =>
                                                updateModalDraft({
                                                    type: event.target.value,
                                                    value:
                                                        event.target.value ===
                                                        "repeater"
                                                            ? []
                                                            : "",
                                                })
                                            }
                                        >
                                            {FIELD_TYPES.map((type) => (
                                                <option
                                                    key={type.value}
                                                    value={type.value}
                                                >
                                                    {type.label}
                                                </option>
                                            ))}
                                        </select>
                                    </div>
                                    <div className="col-md-3">
                                        <label className="form-label">
                                            Placeholder
                                        </label>
                                        <input
                                            className="form-control"
                                            value={modal.draft.placeholder}
                                            onChange={(event) =>
                                                updateModalDraft({
                                                    placeholder:
                                                        event.target.value,
                                                })
                                            }
                                        />
                                    </div>
                                    {["select", "radio"].includes(
                                        modal.draft.type,
                                    ) && (
                                        <div className="col-md-8">
                                            <label className="form-label">
                                                Options
                                            </label>
                                            <input
                                                className="form-control"
                                                value={modal.draft.options}
                                                onChange={(event) =>
                                                    updateModalDraft({
                                                        options:
                                                            event.target.value,
                                                    })
                                                }
                                                placeholder="Option 1, Option 2"
                                            />
                                        </div>
                                    )}
                                </div>

                                {modal.draft.type === "repeater" && (
                                    <div className="border-top mt-4 pt-4">
                                        <h6 className="mb-3">
                                            Repeater Fields
                                        </h6>
                                        <div className="border rounded bg-light p-3 mb-3">
                                            <div className="row g-3">
                                                <div className="col-md-3">
                                                    <label className="form-label">
                                                        Label
                                                    </label>
                                                    <input
                                                        className="form-control"
                                                        value={
                                                            modal.childDraft
                                                                .label
                                                        }
                                                        onChange={(event) =>
                                                            updateChildDraft({
                                                                label: event
                                                                    .target
                                                                    .value,
                                                                name: cleanName(
                                                                    event.target
                                                                        .value,
                                                                    modal
                                                                        .childDraft
                                                                        .name ||
                                                                        "item_field",
                                                                ),
                                                            })
                                                        }
                                                    />
                                                </div>
                                                <div className="col-md-3">
                                                    <label className="form-label">
                                                        Name
                                                    </label>
                                                    <input
                                                        className="form-control"
                                                        value={
                                                            modal.childDraft
                                                                .name
                                                        }
                                                        onChange={(event) =>
                                                            updateChildDraft({
                                                                name: event
                                                                    .target
                                                                    .value,
                                                            })
                                                        }
                                                    />
                                                </div>
                                                <div className="col-md-3">
                                                    <label className="form-label">
                                                        Type
                                                    </label>
                                                    <select
                                                        className="form-select"
                                                        value={
                                                            modal.childDraft
                                                                .type
                                                        }
                                                        onChange={(event) =>
                                                            updateChildDraft({
                                                                type: event
                                                                    .target
                                                                    .value,
                                                            })
                                                        }
                                                    >
                                                        {CHILD_FIELD_TYPES.map(
                                                            (type) => (
                                                                <option
                                                                    key={
                                                                        type.value
                                                                    }
                                                                    value={
                                                                        type.value
                                                                    }
                                                                >
                                                                    {type.label}
                                                                </option>
                                                            ),
                                                        )}
                                                    </select>
                                                </div>
                                                <div className="col-md-3">
                                                    <label className="form-label">
                                                        Placeholder
                                                    </label>
                                                    <input
                                                        className="form-control"
                                                        value={
                                                            modal.childDraft
                                                                .placeholder
                                                        }
                                                        onChange={(event) =>
                                                            updateChildDraft({
                                                                placeholder:
                                                                    event.target
                                                                        .value,
                                                            })
                                                        }
                                                    />
                                                </div>
                                                {["select", "radio"].includes(
                                                    modal.childDraft.type,
                                                ) && (
                                                    <div className="col-md-8">
                                                        <label className="form-label">
                                                            Options
                                                        </label>
                                                        <input
                                                            className="form-control"
                                                            value={
                                                                modal.childDraft
                                                                    .options
                                                            }
                                                            onChange={(event) =>
                                                                updateChildDraft(
                                                                    {
                                                                        options:
                                                                            event
                                                                                .target
                                                                                .value,
                                                                    },
                                                                )
                                                            }
                                                            placeholder="Option 1, Option 2"
                                                        />
                                                    </div>
                                                )}
                                                <div className="col-md-2 d-flex align-items-end">
                                                    <button
                                                        type="button"
                                                        className="btn btn-primary w-100"
                                                        onClick={addChildField}
                                                    >
                                                        <i className="bx bx-plus me-1"></i>
                                                        Add
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        {(modal.draft.fields || []).length ===
                                        0 ? (
                                            <div className="alert alert-info mb-0">
                                                Add child fields like Name and
                                                Email. Each repeater item will
                                                contain these fields.
                                            </div>
                                        ) : (
                                            <div className="table-responsive">
                                                <table className="table table-sm align-middle">
                                                    <thead>
                                                        <tr>
                                                            <th>Label</th>
                                                            <th>Name</th>
                                                            <th>Type</th>
                                                            <th width="80">
                                                                Action
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        {modal.draft.fields.map(
                                                            (
                                                                child,
                                                                childIndex,
                                                            ) => (
                                                                <tr
                                                                    key={`${child.name}_${childIndex}`}
                                                                >
                                                                    <td>
                                                                        {
                                                                            child.label
                                                                        }
                                                                    </td>
                                                                    <td>
                                                                        <code>
                                                                            {
                                                                                child.name
                                                                            }
                                                                        </code>
                                                                    </td>
                                                                    <td>
                                                                        {CHILD_FIELD_TYPES.find(
                                                                            (
                                                                                type,
                                                                            ) =>
                                                                                type.value ===
                                                                                child.type,
                                                                        )
                                                                            ?.label ||
                                                                            child.type}
                                                                    </td>
                                                                    <td>
                                                                        <button
                                                                            type="button"
                                                                            className="btn btn-sm btn-outline-danger"
                                                                            onClick={() =>
                                                                                removeChildField(
                                                                                    childIndex,
                                                                                )
                                                                            }
                                                                        >
                                                                            <i className="bx bx-trash"></i>
                                                                        </button>
                                                                    </td>
                                                                </tr>
                                                            ),
                                                        )}
                                                    </tbody>
                                                </table>
                                            </div>
                                        )}
                                    </div>
                                )}
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    onClick={() => setModal(null)}
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-primary"
                                    onClick={saveModalField}
                                >
                                    Save Field
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            )}
        </form>
    );
}
