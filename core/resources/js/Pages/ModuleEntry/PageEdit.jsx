import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const PageEdit = ({ module, entry, entryLabel, page, tabs = [] }) => {
    const { data, setData, put, errors, processing } = useForm({
        title: page.title || '',
        slug: page.slug || '',
        display_location: page.display_location || '',
        is_published: page.is_published ?? true,
        target_blank: page.target_blank ?? false,
        tab_id: page.tab_id || '',
        display_order: page.display_order ?? 1000,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route('modules.entries.pages.update', { module: module.id, entry: entry.id, page: page.id }));
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Edit Entry Page</h1>
                    <p className="text-muted mb-0">{module?.name} — {entryLabel}</p>
                </div>
                <Link
                    href={route('modules.entries.pages', { module: module.id, entry: entry.id })}
                    className="btn btn-secondary"
                >
                    <i className="bx bx-arrow-back me-1"></i>
                    Back
                </Link>
            </div>

            <div className="card">
                <form onSubmit={handleSubmit}>
                    <div className="card-body">
                        <div className="row">
                            <div className="mb-3 col-md-6">
                                <label className="form-label">Page Title <span className="text-danger">*</span></label>
                                <input
                                    type="text"
                                    className={`form-control ${errors.title ? 'is-invalid' : ''}`}
                                    value={data.title}
                                    onChange={(e) => setData('title', e.target.value)}
                                />
                                {errors.title && <div className="invalid-feedback">{errors.title}</div>}
                            </div>

                            <div className="mb-3 col-md-6">
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
                                <div className="form-text">
                                    Entry slug prefix is fixed. Full URL: <code>{page.full_slug}</code>
                                </div>
                            </div>

                            <div className="mb-3 col-md-6">
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

                            <div className="mb-3 col-md-6">
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

                            <div className="mb-3 col-md-4">
                                <label className="form-label">Display Order</label>
                                <input
                                    type="number"
                                    className="form-control"
                                    value={data.display_order}
                                    onChange={(e) => setData('display_order', e.target.value)}
                                />
                            </div>

                            <div className="mb-3 col-md-4">
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

                            <div className="mb-3 col-md-4">
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
                            <button type="submit" className="btn btn-primary me-2" disabled={processing}>
                                {processing ? 'Saving...' : 'Save Page'}
                            </button>
                            <Link
                                href={route('modules.entries.pages.sections', { module: module.id, entry: entry.id, page: page.id })}
                                className="btn btn-outline-primary me-2"
                            >
                                Manage Sections
                            </Link>
                            <Link
                                href={route('modules.entries.pages', { module: module.id, entry: entry.id })}
                                className="btn btn-secondary"
                            >
                                Cancel
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
};

export default PageEdit;
