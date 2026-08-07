import React from 'react';
import { Link, useForm } from '@inertiajs/react';
import useCtrlSSubmit from '@/hooks/useCtrlSSubmit';

const Create = () => {
    const { data, setData, post, errors, processing } = useForm({
        heading: '',
        subheading: '',
        display_order: 0,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post(route('tabs.store'));
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Create Tab</h1>
                    <p className="text-muted mb-0">Add a new group for pages</p>
                </div>
            </div>
            
            <div className="card">
                <form onSubmit={handleSubmit}>
                    <div className="card-body">
                        <div className="row">
                            <div className="mb-3 col-md-6">
                                <label className="form-label">Heading <span className='text-danger'>*</span></label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={data.heading}
                                    onChange={e => setData('heading', e.target.value)}
                                    placeholder="e.g. About"
                                />
                                {errors.heading && <div className="text-danger small">{errors.heading}</div>}
                            </div>

                            <div className="mb-3 col-md-6">
                                <label className="form-label">Subheading</label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={data.subheading}
                                    onChange={e => setData('subheading', e.target.value)}
                                    placeholder="e.g. Learn more about us"
                                />
                                {errors.subheading && <div className="text-danger small">{errors.subheading}</div>}
                            </div>

                            <div className="mb-3 col-md-4">
                                <label className="form-label">Display Order</label>
                                <input
                                    type="number"
                                    className="form-control"
                                    value={data.display_order}
                                    onChange={e => setData('display_order', e.target.value)}
                                />
                                {errors.display_order && <div className="text-danger small">{errors.display_order}</div>}
                                <div className="form-text">Higher numbers appear first</div>
                            </div>
                        </div>

                        <div className="mt-4 pt-3 border-top">
                            <button 
                                type="submit" 
                                className="btn btn-primary me-2"
                                disabled={processing}
                            >
                                {processing ? 'Creating...' : 'Create Tab'}
                            </button>
                            <Link
                                href={route('tabs.index')} 
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

export default Create;
