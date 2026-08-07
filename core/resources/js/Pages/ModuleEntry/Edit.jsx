import React, { useMemo, useState, useEffect } from 'react';
import { Link, useForm, router } from '@inertiajs/react';
import { buildEmptyData, renderFieldInput } from './entryFormHelpers';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Edit = ({ module, entry, mappedModuleEntries = {} }) => {
    const fields = useMemo(() => (Array.isArray(module?.fields_config) ? module.fields_config : []), [module]);
    const mappingEnabled = !!module?.mapping_enabled;
    const isAssocObject = (obj) => obj && typeof obj === 'object' && !Array.isArray(obj);
    const normalizeMappingConfig = (raw) => {
        if (!Array.isArray(raw)) return [];
        if (raw[0] && isAssocObject(raw[0]) && Array.isArray(raw[0].fields)) {
            return raw.map((g) => ({
                group_label: g.group_label || 'Repeatable Items',
                group_name: g.group_name || 'items',
                fields: Array.isArray(g.fields) ? g.fields : [],
            }));
        }
        if (raw[0] && isAssocObject(raw[0]) && typeof raw[0].name === 'string') {
            return [{ group_label: 'Repeatable Items', group_name: 'items', fields: raw }];
        }
        return [];
    };
    const mappingGroups = useMemo(() => normalizeMappingConfig(module?.mapping_config || []), [module]);
    const mappingFieldsFlat = useMemo(() => (mappingGroups || []).flatMap((g) => g.fields || []), [mappingGroups]);
    const typesEnabled = !!module?.types_enabled;
    const types = useMemo(() => (Array.isArray(module?.types) ? module.types : []), [module]);

    const entryData = entry?.data || {};
    const mappingData = useMemo(() => {
        const mi = entryData?.mapping_items;
        if (mi && isAssocObject(mi)) {
            return mi;
        }
        if (Array.isArray(mi) && mi.length > 0) {
            return { items: mi };
        }
        const legacy = {};
        mappingGroups.forEach((g) => {
            const groupName = g.group_name || 'items';
            const maxLen = Math.max(...(g.fields || []).map((f) => Array.isArray(entryData?.[f.name]) ? entryData[f.name].length : 0), 0);
            const items = [];
            for (let i = 0; i < maxLen; i++) {
                const item = {};
                (g.fields || []).forEach((f) => { item[f.name] = Array.isArray(entryData?.[f.name]) ? (entryData[f.name][i] ?? '') : ''; });
                items.push(item);
            }
            legacy[groupName] = items;
        });
        return legacy;
    }, [entryData, mappingGroups]);

    const mappingFieldNames = useMemo(() => new Set((mappingFieldsFlat || []).map((f) => f.name).filter(Boolean)), [mappingFieldsFlat]);
    const regularData = useMemo(() => {
        const d = { ...entryData };
        delete d.mapping_items;
        mappingFieldNames.forEach((n) => {
            if (Array.isArray(d?.[n])) delete d[n];
        });
        return d;
    }, [entryData, mappingFieldNames]);

    const [files, setFiles] = useState({});
    const [filePreviews, setFilePreviews] = useState({});
    const [mappingFiles, setMappingFiles] = useState({});
    const [mappingFilePreviews, setMappingFilePreviews] = useState({});
    const [deletedFiles, setDeletedFiles] = useState(new Set()); 
    const [formErrors, setFormErrors] = useState({});
    const [isSubmitting, setIsSubmitting] = useState(false);

    const initialEntryData = useMemo(() => {
        const base = { ...buildEmptyData(fields), ...regularData };
        (module?.selectbox_modules_data || []).forEach((m) => {
            if (base[m.slug] === undefined) {
                base[m.slug] = '';
            }
        });
        return base;
    }, [fields, regularData, module]);

    const { data, setData, errors } = useForm({
        type: entryData.type || '',
        data: initialEntryData,
        slug: entry?.slug || '',
        mapping_data: mappingData,
        sort_order: entry?.sort_order ?? 0,
        is_published: entry?.is_published ?? true,
    });

    useEffect(() => {
        const initialPreviews = {};
        fields.forEach(field => {
            if (['file', 'image'].includes(field.type)) {
                const value = entryData[field.name];
                if (value && typeof value === 'string') {
                    initialPreviews[field.name] = value;
                }
            }
        });
        setFilePreviews(initialPreviews);
    }, [entryData, fields]);

    useEffect(() => {
        const initialMappingPreviews = {};
        const mi = entryData?.mapping_items;
        if (mi && isAssocObject(mi)) {
            Object.entries(mi).forEach(([groupName, items]) => {
                if (!Array.isArray(items)) return;
                const group = mappingGroups.find((g) => (g.group_name || 'items') === groupName);
                (items || []).forEach((item, idx) => {
                    (group?.fields || []).forEach((field) => {
                        if (!['file', 'image'].includes(field.type)) return;
                        const v = item?.[field.name];
                        if (v && typeof v === 'string') {
                            initialMappingPreviews[`${groupName}_${idx}_${field.name}`] = v;
                        }
                    });
                });
            });
        }
        setMappingFilePreviews(initialMappingPreviews);
    }, [entryData, mappingGroups]);

    const buildEmptyMappingItem = (fields) => {
        const item = {};
        (fields || []).forEach((f) => { if (f?.name) item[f.name] = f.type === 'checkbox' ? false : ''; });
        return item;
    };

    const addMappingItem = (groupName) => {
        const group = (mappingGroups || []).find((g) => (g.group_name || 'items') === groupName) || mappingGroups?.[0];
        const empty = buildEmptyMappingItem(group?.fields || []);
        setData('mapping_data', {
            ...(data.mapping_data || {}),
            [groupName]: [...(data.mapping_data?.[groupName] || []), empty],
        });
    };

    const removeMappingItem = (groupName, index) => {
        const arr = [...(data.mapping_data?.[groupName] || [])];
        arr.splice(index, 1);
        setData('mapping_data', { ...(data.mapping_data || {}), [groupName]: arr });
    };

    const updateMappingItem = (groupName, index, fieldName, value) => {
        const arr = [...(data.mapping_data?.[groupName] || [])];
        const item = isAssocObject(arr[index]) ? arr[index] : {};
        arr[index] = { ...item, [fieldName]: value };
        setData('mapping_data', { ...(data.mapping_data || {}), [groupName]: arr });
    };

    const handleFileSelect = (fieldName, file) => {
        const newFiles = { ...files };
        newFiles[fieldName] = file;
        setFiles(newFiles);
        
        if (file.type.startsWith('image/')) {
            const previewUrl = URL.createObjectURL(file);
            const newPreviews = { ...filePreviews };
            newPreviews[fieldName] = previewUrl;
            setFilePreviews(newPreviews);
        }
    };

    const handleRemoveFile = (fieldName) => {
        const newFiles = { ...files };
        delete newFiles[fieldName];
        setFiles(newFiles);
        
        if (filePreviews[fieldName] && filePreviews[fieldName].startsWith('blob:')) {
            URL.revokeObjectURL(filePreviews[fieldName]);
        }
        
        const newPreviews = { ...filePreviews };
        delete newPreviews[fieldName];
        setFilePreviews(newPreviews);
        
        const newDeletedFiles = new Set(deletedFiles);
        newDeletedFiles.add(fieldName);
        setDeletedFiles(newDeletedFiles);
        
        setData('data', { ...data.data, [fieldName]: '' });
    };

    const handleMappingFileSelect = (groupName, itemIndex, fieldName, file) => {
        const fileKey = `${groupName}_${itemIndex}_${fieldName}`;
        setMappingFiles((prev) => ({ ...prev, [fileKey]: file }));
        if (file.type.startsWith('image/')) {
            const previewUrl = URL.createObjectURL(file);
            setMappingFilePreviews((prev) => ({ ...prev, [fileKey]: previewUrl }));
        }
        updateMappingItem(groupName, itemIndex, fieldName, file);
    };

    const handleRemoveMappingFile = (groupName, itemIndex, fieldName) => {
        const fileKey = `${groupName}_${itemIndex}_${fieldName}`;
        setMappingFiles((prev) => {
            const n = { ...prev };
            delete n[fileKey];
            return n;
        });
        setMappingFilePreviews((prev) => {
            const n = { ...prev };
            if (n[fileKey]?.startsWith('blob:')) URL.revokeObjectURL(n[fileKey]);
            delete n[fileKey];
            return n;
        });
        updateMappingItem(groupName, itemIndex, fieldName, '');
    };

    const hasSlugField = fields.some((f) => f.is_slug);

    const handleSubmit = (e) => {
        e.preventDefault();
        setFormErrors({});
        setIsSubmitting(true);
        
        const formData = new FormData();
        formData.append('slug', data.slug || '');
        formData.append('type', data.type);
        formData.append('sort_order', data.sort_order);
        formData.append('is_published', data.is_published);
        formData.append('_deleted_files', JSON.stringify(Array.from(deletedFiles))); 

        fields.forEach(field => {
            const key = field.name;
            const value = data.data[key];
            const isFileType = ['file', 'image'].includes(field.type);
            
            if (isFileType) {
                if (files[key]) {
                    formData.append(`data[${key}]`, files[key]);
                } else if (deletedFiles.has(key)) {
                    formData.append(`data[${key}]`, '');
                } else if (value) {
                    formData.append(`data[${key}]`, value);
                } else {
                    formData.append(`data[${key}]`, '');
                }
            } else {
                formData.append(`data[${key}]`, value || '');
            }
        });

        (module?.selectbox_modules_data || []).forEach((m) => {
            const key = m.slug;
            const value = data.data[key];
            formData.append(`data[${key}]`, value || '');
        });

        if (mappingEnabled) {
            Object.entries(data.mapping_data || {}).forEach(([groupName, items]) => {
                if (!Array.isArray(items)) return;
                items.forEach((item, itemIndex) => {
                    const obj = isAssocObject(item) ? item : {};
                    Object.entries(obj).forEach(([fieldName, value]) => {
                        const fileKey = `${groupName}_${itemIndex}_${fieldName}`;
                        if (mappingFiles[fileKey]) {
                            formData.append(`mapping_data[${groupName}][${itemIndex}][${fieldName}]`, mappingFiles[fileKey]);
                        } else {
                            formData.append(`mapping_data[${groupName}][${itemIndex}][${fieldName}]`, value ?? '');
                        }
                    });
                });
            });
        }

        router.post(route('modules.entries.update', { module: module.id, entry: entry.id }), formData, {
            preserveScroll: true,
            onError: (errors) => {
                setFormErrors(errors);
                setIsSubmitting(false);
            },
            onSuccess: () => {
                setIsSubmitting(false);
            },
        });
    };

    useCtrlSSubmit(handleSubmit, !isSubmitting);

    return (
        <>
            <h1 className="text-muted">Edit {module?.name}</h1>

            <div className="card">
                <form onSubmit={handleSubmit} encType="multipart/form-data">
                    <div className="card-body">
                        <div className="row g-3">
                            {typesEnabled && types.length > 0 && (
                                <div className="col-md-6">
                                    <label className="form-label">
                                        Type <span className="text-danger">*</span>
                                    </label>
                                    <select
                                        className={`form-select ${errors?.type ? 'is-invalid' : ''}`}
                                        value={data.type}
                                        onChange={(e) => setData('type', e.target.value)}
                                        required
                                    >
                                        <option value="">Select Type</option>
                                        {types.map((type, idx) => (
                                            <option key={idx} value={type}>
                                                {type}
                                            </option>
                                        ))}
                                    </select>
                                    {errors?.type && <div className="text-danger small">{errors.type}</div>}
                                </div>
                            )}
                            {fields.map((field) => (
                                <div key={field.name} className="col-md-6">
                                    <label className="form-label">
                                        {field.label || field.name}
                                        {field.required && <span className="text-danger"> *</span>}
                                    </label>
                                    {renderFieldInput(
                                        field, 
                                        data.data[field.name], 
                                        (v) => setData('data', { ...data.data, [field.name]: v }),
                                        {
                                            moduleEntries: mappedModuleEntries,
                                            onFileSelect: (file) => handleFileSelect(field.name, file),
                                            onRemoveFile: () => handleRemoveFile(field.name),
                                            filePreview: files[field.name] || filePreviews[field.name],
                                            imagePreviewUrl: filePreviews[field.name]
                                        }
                                    )}
                                    {errors?.[`data.${field.name}`] && (
                                        <div className="text-danger small mt-2">
                                            {Array.isArray(errors[`data.${field.name}`]) 
                                                ? errors[`data.${field.name}`].join(', ') 
                                                : errors[`data.${field.name}`]}
                                        </div>
                                    )}
                                    {formErrors?.[`data.${field.name}`] && (
                                        <div className="text-danger small mt-2">
                                            {Array.isArray(formErrors[`data.${field.name}`]) 
                                                ? formErrors[`data.${field.name}`].join(', ') 
                                                : formErrors[`data.${field.name}`]}
                                        </div>
                                    )}
                                </div>
                            ))}

                            {(module?.selectbox_modules_data || []).map((m) => (
                                <div key={m.id} className="col-md-6">
                                    <label className="form-label">
                                        {m.name}
                                    </label>
                                    <select
                                        className="form-select"
                                        value={data.data[m.slug] ?? ''}
                                        onChange={(e) => setData('data', { ...data.data, [m.slug]: e.target.value })}
                                    >
                                        <option value="">Select {m.name}</option>
                                        {(m.options || []).map((opt) => (
                                            <option key={opt.id} value={opt.id}>
                                                {opt.label}
                                            </option>
                                        ))}
                                    </select>
                                    {errors?.[`data.${m.slug}`] && (
                                        <div className="text-danger small mt-2">
                                            {errors[`data.${m.slug}`]}
                                        </div>
                                    )}
                                    {formErrors?.[`data.${m.slug}`] && (
                                        <div className="text-danger small mt-2">
                                            {formErrors[`data.${m.slug}`]}
                                        </div>
                                    )}
                                </div>
                            ))}

                            {hasSlugField && (
                                <div className="col-md-6">
                                    <label className="form-label">
                                        URL Slug <span className="text-danger">*</span>
                                    </label>
                                    <input
                                        type="text"
                                        className={`form-control ${(errors?.slug || formErrors?.slug) ? 'is-invalid' : ''}`}
                                        value={data.slug || ''}
                                        onChange={(e) => setData('slug', e.target.value)}
                                    />
                                    <div className="form-text">
                                        {fields.find((f) => f.is_slug)
                                            ? `Will be generated from "${fields.find((f) => f.is_slug).label || fields.find((f) => f.is_slug).name}" if left empty.`
                                            : 'Enter a unique URL-friendly slug.'}
                                    </div>
                                    {errors?.slug && <div className="text-danger small">{errors.slug}</div>}
                                    {formErrors?.slug && <div className="text-danger small">{formErrors.slug}</div>}  {/* ADD THIS */}
                                </div>
                            )}

                            {mappingEnabled && mappingGroups.length > 0 && (
                                <div className="col-12">
                                    <h6 className="mb-3">Repeatable Groups</h6>
                                    <div className="d-flex flex-column gap-4">
                                        {mappingGroups.map((g) => (
                                            <div key={g.group_name || g.group_label} className="border rounded p-3">
                                                <div className="d-flex justify-content-between align-items-center mb-3">
                                                    <strong>{g.group_label || 'Repeatable Items'} <small className="text-muted">({g.group_name || 'items'})</small></strong>
                                                    <button
                                                        type="button"
                                                        className="btn btn-sm btn-outline-primary"
                                                        onClick={() => addMappingItem(g.group_name || 'items')}
                                                    >
                                                        <i className="bx bx-plus me-1"></i>
                                                        Add Item
                                                    </button>
                                                </div>
                                                {(data.mapping_data?.[g.group_name || 'items'] || []).length === 0 ? (
                                                    <div className="text-muted small">No items yet.</div>
                                                ) : (
                                                    <div className="d-flex flex-column gap-2">
                                                        {(data.mapping_data?.[g.group_name || 'items'] || []).map((item, idx) => (
                                                            <div key={idx} className="border rounded p-2">
                                                                <div className="d-flex justify-content-between align-items-center mb-2">
                                                                    <span className="text-muted small">Item #{idx + 1}</span>
                                                                    <button type="button" className="btn btn-sm btn-outline-danger" onClick={() => removeMappingItem(g.group_name || 'items', idx)}>
                                                                        <i className="bx bx-trash"></i>
                                                                    </button>
                                                                </div>
                                                                <div className="row g-2">
                                                                    {(g.fields || []).map((mf) => (
                                                                        <div key={mf.name} className={mf.type === 'code' || mf.type === 'textarea' ? 'col-12' : 'col-md-6'}>
                                                                            <label className="form-label small">
                                                                                {mf.label || mf.name}
                                                                                {mf.required && <span className="text-danger"> *</span>}
                                                                            </label>
                                                                            {renderFieldInput(
                                                                                mf,
                                                                                item?.[mf.name],
                                                                                (v) => updateMappingItem(g.group_name || 'items', idx, mf.name, v),
                                                                                {
                                                                                    moduleEntries: mappedModuleEntries,
                                                                                    onFileSelect: (file) => handleMappingFileSelect(g.group_name || 'items', idx, mf.name, file),
                                                                                    onRemoveFile: () => handleRemoveMappingFile(g.group_name || 'items', idx, mf.name),
                                                                                    filePreview: mappingFiles[`${g.group_name || 'items'}_${idx}_${mf.name}`] || mappingFilePreviews[`${g.group_name || 'items'}_${idx}_${mf.name}`],
                                                                                    imagePreviewUrl: mappingFilePreviews[`${g.group_name || 'items'}_${idx}_${mf.name}`],
                                                                                }
                                                                            )}
                                                                            {errors?.[`mapping_data.${g.group_name || 'items'}.${idx}.${mf.name}`] && (
                                                                                <div className="text-danger small mt-1">{errors[`mapping_data.${g.group_name || 'items'}.${idx}.${mf.name}`]}</div>
                                                                            )}
                                                                            {formErrors?.[`mapping_data.${g.group_name || 'items'}.${idx}.${mf.name}`] && (
                                                                                <div className="text-danger small mt-1">{formErrors[`mapping_data.${g.group_name || 'items'}.${idx}.${mf.name}`]}</div>
                                                                            )}
                                                                        </div>
                                                                    ))}
                                                                </div>
                                                            </div>
                                                        ))}
                                                    </div>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                </div>
                            )}

                            <div className="col-12 mt-2">
                                <button type="submit" className="btn btn-primary me-2" disabled={isSubmitting}>
                                    {isSubmitting ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-2" />
                                            Saving...
                                        </>
                                    ) : (
                                        <>
                                            <i className="bx bx-save me-2"></i>
                                            Update
                                        </>
                                    )}
                                </button>
                                <Link href={route('modules.entries.index', module.id)} className="btn btn-secondary">
                                    Cancel
                                </Link>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
};

export default Edit;
