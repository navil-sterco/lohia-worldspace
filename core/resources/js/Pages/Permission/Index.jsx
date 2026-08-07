import React, { useEffect, useRef, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import { ToastContainer } from 'react-toastify';
import { useFlashMessage } from '@/hooks/useFlashMessage';
import ActionDropdown from '@/Components/ActionDropdown';
import Pagination from '@/Components/Pagination';

const Index = ({ permissions }) => {
    useFlashMessage();
    const modalRef = useRef(null);
    const modalInstance = useRef(null);
    const [idDelete, setIdDelete] = useState(null);
    const [nameDelete, setNameDelete] = useState('');
    const { delete: destroy, processing } = useForm();

    useEffect(() => {
        if (modalRef.current) {
            modalInstance.current = new bootstrap.Modal(modalRef.current);
        }
    }, []);

    const showDeleteModal = (id, name) => {
        setIdDelete(id);
        setNameDelete(name);
        modalInstance.current?.show();
    };

    const handleConfirmDelete = () => {
        destroy(route('permissions.destroy', idDelete), {
            preserveScroll: true,
            onSuccess: () => {
                modalInstance.current?.hide();
                setIdDelete(null);
                setNameDelete('');
            },
        });
    };

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Permissions</h1>
                    <p className="text-muted mb-0">Define granular access permissions</p>
                </div>
                <Link href={route('permissions.create')} className="btn btn-primary">
                    <i className="bx bx-plus me-2"></i>
                    Add Permission
                </Link>
            </div>

            <ToastContainer />

            <div className="card">
                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead>
                            <tr>
                                <th>Permission</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody className="table-border-bottom-0">
                            {permissions.data.length > 0 ? (
                                permissions.data.map((permission) => {
                                    const actions = [
                                        {
                                            label: 'Edit',
                                            icon: 'bx-edit-alt',
                                            href: route('permissions.edit', permission.id),
                                        },
                                        {
                                            label: 'Delete',
                                            icon: 'bx-trash',
                                            onClick: () =>
                                                showDeleteModal(permission.id, permission.name),
                                            className: 'text-danger',
                                        },
                                    ];

                                    return (
                                        <tr key={permission.id}>
                                            <td>
                                                <i className="bx bx-key bx-sm me-3"></i>
                                                {permission.name}
                                            </td>
                                            <td>{permission.created_at}</td>
                                            <td>
                                                <ActionDropdown actions={actions} />
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan="3" className="text-center py-4">
                                        <div className="text-muted">
                                            <i className="bx bx-info-circle bx-sm me-2"></i>
                                            No permissions found
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>

                <div className="modal fade" ref={modalRef} tabIndex="-1">
                    <div className="modal-dialog modal-dialog-centered">
                        <div className="modal-content">
                            <div className="modal-header">
                                <h5 className="modal-title">Confirm Deletion</h5>
                                <button type="button" className="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div className="modal-body">
                                <p>
                                    Are you sure you want to delete the permission{' '}
                                    <strong>{nameDelete}</strong>?
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

            {permissions.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {permissions.from ?? 0} to {permissions.to ?? 0} of{' '}
                            {permissions.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={permissions.links} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;
