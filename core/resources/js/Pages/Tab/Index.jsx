import React, { useEffect, useRef, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { router, usePage } from '@inertiajs/react';
import { ToastContainer, toast } from 'react-toastify';

const Index = ({ tabs }) => {
    const { flash } = usePage().props;
    const modalRef = useRef(null);
    const modalInstance = useRef(null);
    const [idDelete, setIdDelete] = useState(null);
    const { delete: destroy, processing } = useForm();

    useEffect(() => {
        if (flash.success) toast.success(flash.success);
    }, [flash]);

    useEffect(() => {
        if (modalRef.current) {
            modalInstance.current = new bootstrap.Modal(modalRef.current);
        }
    }, []);

    const showDeleteModal = (id) => {
        setIdDelete(id);
        modalInstance.current.show();
    };

    const handleConfirmDelete = () => {
        destroy(route('tabs.destroy', idDelete), {
            preserveScroll: true,
            onSuccess: () => {
                modalInstance.current.hide();
                setIdDelete(null);
            }
        });
    };

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Page Tabs</h1>
                    <p className="text-muted mb-0">Group pages into navigable tabs with headings</p>
                </div>
                <Link
                    href={route('tabs.create')}
                    className="btn btn-primary"
                >
                    <i className='bx bx-plus me-2'></i>
                    Create Tab
                </Link>
            </div>

            <ToastContainer />
            
            <div className="card">
                <div className="table-responsive">
                    <table className="table table-hover">
                        <thead className="table-light">
                            <tr>
                                <th>Heading</th>
                                <th>Subheading</th>
                                <th>Order</th>
                                <th>Pages Count</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {tabs.length > 0 ? (
                                tabs.map((tab) => (
                                    <tr key={tab.id}>
                                        <td>
                                            <strong>{tab.heading}</strong>
                                        </td>
                                        <td>
                                            <span className="text-muted">{tab.subheading || '-'}</span>
                                        </td>
                                        <td>
                                            <span className="badge bg-label-primary">{tab.display_order}</span>
                                        </td>
                                        <td>
                                            <span className="badge bg-secondary">{tab.pages_count} Pages</span>
                                        </td>
                                        <td>
                                            <div className="d-flex align-items-center gap-1">
                                                <Link
                                                    href={route('tabs.edit', tab.id)}
                                                    className="btn btn-sm btn-icon btn-outline-primary"
                                                    title="Edit"
                                                >
                                                    <i className="bx bx-edit-alt"></i>
                                                </Link>
                                                <button
                                                    onClick={() => showDeleteModal(tab.id)}
                                                    className="btn btn-sm btn-icon btn-outline-danger"
                                                    title="Delete"
                                                    disabled={processing}
                                                >
                                                    <i className="bx bx-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="5" className="text-center py-4">
                                        <div className="text-muted">
                                            <i className="bx bx-folder-open bx-lg mb-2"></i>
                                            <p>No tabs found</p>
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                {/* Delete Confirmation Modal */}
                <div className="modal fade" ref={modalRef} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Confirm Deletion</h5>
                                <button type="button" className="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div className="modal-body">
                                <p>
                                    Are you sure you want to delete this tab? 
                                    Pages linked to this tab will remain but will become unassigned.
                                </p>
                            </div>
                            <div className="modal-footer">
                                <button type="button" className="btn btn-secondary" data-bs-dismiss="modal">
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={handleConfirmDelete}
                                    disabled={processing}
                                >
                                    {processing ? 'Deleting...' : 'Yes, Delete'}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Index;
