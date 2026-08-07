import React, { useEffect, useMemo, useState } from 'react';
import { Link, router, useForm } from '@inertiajs/react';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';
import { useModal } from '@/hooks/useModal';

const slugify = (value) =>
    (value || '')
        .toString()
        .toLowerCase()
        .replace(/[^a-z0-9\s-]/g, '')
        .replace(/\s+/g, '-')
        .replace(/-+/g, '-')
        .replace(/^-+|-+$/g, '');

export function EntryPagesPanel({
    module,
    entry,
    entryLabel,
    attachedPages = [],
    tabs = [],
    hasModuleSections = true,
    embedded = false,
}) {
    const [pageRows, setPageRows] = useState(attachedPages);
    const { modalRef, show, hide } = useModal();
    const [itemToDelete, setItemToDelete] = useState(null);
    const [deleting, setDeleting] = useState(false);

    useEffect(() => {
        setPageRows(attachedPages);
    }, [attachedPages]);

    const { data, setData, post, processing, errors, reset } = useForm({
        title: '',
        slug: '',
        display_location: '',
        is_published: true,
        target_blank: false,
        tab_id: '',
        display_order: 1000,
    });

    const slugPreview = useMemo(() => {
        const child = data.slug ? slugify(data.slug.split('/').pop()) : slugify(data.title);
        return `${entry.slug_prefix || entry.slug || 'entry-slug'}/${child || 'page-slug'}`;
    }, [data.slug, data.title, entry.slug_prefix, entry.slug]);

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('modules.entries.pages.store', { module: module.id, entry: entry.id }), {
            preserveScroll: true,
            onSuccess: () => reset(),
        });
    };

    const confirmDelete = (page) => {
        setItemToDelete(page);
        show();
    };

    const handleDelete = () => {
        if (!itemToDelete?.id) return;
        setDeleting(true);
        router.delete(
            route('modules.entries.pages.detach', {
                module: module.id,
                entry: entry.id,
                page: itemToDelete.id,
            }),
            {
                preserveScroll: true,
                onFinish: () => setDeleting(false),
                onSuccess: () => {
                    hide();
                    setItemToDelete(null);
                },
            }
        );
    };

    return (
        <div id="entry-pages" className={embedded ? 'mt-4' : ''}>
            {!embedded && (
                <div className="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 className="text-muted mb-1">Entry Pages</h1>
                        <p className="text-muted mb-0">{module?.name} — {entryLabel}</p>
                        <p className="text-muted small mb-0">
                            Base slug: <code>{entry.slug_prefix || entry.slug}</code>
                        </p>
                    </div>
                    <Link href={route('modules.entries.index', module.id)} className="btn btn-secondary">
                        <i className="bx bx-arrow-back me-1"></i>
                        Back
                    </Link>
                </div>
            )}

            {embedded && (
                <div className="mb-3">
                    <h5 className="text-muted mb-1">CMS Pages</h5>
                    <p className="text-muted small mb-0">
                        Create small pages under <code>{entry.slug_prefix || entry.slug}</code>. Sections use the same templates mapped for module detail.
                    </p>
                </div>
            )}

            <div className="card mb-4">
                <div className="card-header">
                    <h5 className="card-title mb-0">Create Page</h5>
                </div>
                <form onSubmit={handleSubmit}>
                    <div className="card-body">
                        <div className="row g-3">
                            <div className="col-md-6">
                                <label className="form-label">Page Title <span className="text-danger">*</span></label>
                                <input
                                    type="text"
                                    className={`form-control ${errors.title ? 'is-invalid' : ''}`}
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                    placeholder="About Computer"
                                    required
                                />
                                {errors.title && <div className="invalid-feedback">{errors.title}</div>}
                            </div>

                            <div className="col-md-6">
                                <label className="form-label">Page Slug</label>
                                <div className="input-group">
                                    <span className="input-group-text">{entry.slug_prefix || entry.slug}/</span>
                                    <input
                                        type="text"
                                        className={`form-control ${errors.slug ? 'is-invalid' : ''}`}
                                        value={data.slug}
                                        onChange={(e) => setData('slug', e.target.value)}
                                        placeholder="about-computer"
                                    />
                                </div>
                                {errors.slug && <div className="invalid-feedback d-block">{errors.slug}</div>}
                                <div className="form-text">Full URL: <code>{slugPreview}</code></div>
                            </div>

                            <div className="col-md-6">
                                <label className="form-label">Link to Tab</label>
                                <select
                                    className="form-select"
                                    value={data.tab_id}
                                    onChange={(e) => setData('tab_id', e.target.value)}
                                >
                                    <option value="">No Tab (General)</option>
                                    {tabs.map((tab) => (
                                        <option key={tab.id} value={tab.id}>{tab.heading}</option>
                                    ))}
                                </select>
                            </div>

                            <div className="col-md-6">
                                <label className="form-label">Show On Display Location</label>
                                <select
                                    className="form-select"
                                    value={data.display_location}
                                    onChange={(e) => setData('display_location', e.target.value)}
                                >
                                    <option value="">Select Display Location</option>
                                    <option value="header">Header</option>
                                    <option value="footer">Footer</option>
                                    <option value="sidebar">Sidebar</option>
                                </select>
                            </div>

                            <div className="col-md-4">
                                <label className="form-label">Display Order</label>
                                <input
                                    type="number"
                                    className="form-control"
                                    value={data.display_order}
                                    onChange={(e) => setData('display_order', e.target.value)}
                                />
                            </div>

                            <div className="col-md-4">
                                <div className="form-check form-switch mt-4">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={data.target_blank}
                                        onChange={(e) => setData('target_blank', e.target.checked)}
                                        role="switch"
                                    />
                                    <label className="form-check-label">Target Blank</label>
                                </div>
                            </div>

                            <div className="col-md-4">
                                <div className="form-check form-switch mt-4">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={data.is_published}
                                        onChange={(e) => setData('is_published', e.target.checked)}
                                        role="switch"
                                    />
                                    <label className="form-check-label">Publish Page</label>
                                </div>
                            </div>
                        </div>

                        <div className="mt-4 pt-3 border-top">
                            <button type="submit" className="btn btn-primary" disabled={processing || !hasModuleSections}>
                                {processing ? 'Creating...' : 'Create Page & Add Sections'}
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div className="card">
                <div className="card-header">
                    <h5 className="card-title mb-0">Pages in this Entry</h5>
                </div>
                <div className="card-body">
                    {pageRows.length === 0 ? (
                        <div className="text-muted">No CMS pages yet.</div>
                    ) : (
                        <div className="table-responsive">
                            <table className="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Title</th>
                                        <th>Slug</th>
                                        <th>Order</th>
                                        <th>Sections</th>
                                        <th>Status</th>
                                        <th className="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {pageRows.map((p) => (
                                        <tr key={p.id}>
                                            <td>{p.title}</td>
                                            <td><code>{p.slug}</code></td>
                                            <td>{p.display_order}</td>
                                            <td>{p.sections_count}</td>
                                            <td>
                                                <span className={`badge ${p.is_published ? 'bg-success' : 'bg-secondary'}`}>
                                                    {p.is_published ? 'Published' : 'Draft'}
                                                </span>
                                                {p.target_blank && <span className="badge bg-info ms-1">New Tab</span>}
                                            </td>
                                            <td className="text-end">
                                                <Link
                                                    href={route('modules.entries.pages.sections', { module: module.id, entry: entry.id, page: p.id })}
                                                    className="btn btn-sm btn-outline-primary me-1"
                                                >
                                                    Sections
                                                </Link>
                                                <Link
                                                    href={route('modules.entries.pages.edit', { module: module.id, entry: entry.id, page: p.id })}
                                                    className="btn btn-sm btn-outline-secondary me-1"
                                                >
                                                    Edit
                                                </Link>
                                                <button
                                                    type="button"
                                                    className="btn btn-sm btn-outline-danger"
                                                    onClick={() => confirmDelete(p)}
                                                >
                                                    Delete
                                                </button>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    )}
                    <p className="text-muted small mt-3 mb-0">
                        Open <strong>Sections</strong> to add content with the same section builder as site pages (drag-and-drop order, mapped templates only).
                    </p>
                </div>
            </div>

            <DeleteConfirmationModal
                modalRef={modalRef}
                title="Confirm Deletion"
                message="Are you sure you want to delete this page and all its sections?"
                itemName={itemToDelete ? itemToDelete.title : null}
                onConfirm={handleDelete}
                onCancel={() => {
                    hide();
                    setItemToDelete(null);
                }}
                processing={deleting}
            />
        </div>
    );
}
