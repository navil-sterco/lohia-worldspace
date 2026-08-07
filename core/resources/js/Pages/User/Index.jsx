import React, { useEffect, useRef, useState } from 'react';
import { useForm } from '@inertiajs/react';
import { ToastContainer } from 'react-toastify';
import { useFlashMessage } from '@/hooks/useFlashMessage';
import TableHeader from '@/Components/TableHeader';
import Pagination from '@/Components/Pagination';
import ActionDropdown from '@/Components/ActionDropdown';
import { useDebouncedSearch } from '@/hooks/useSearch';

const Index = ({ searchTerm, users }) => {
    const [query, setQuery] = useState(searchTerm || '');
    useDebouncedSearch(query, 'users');
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
        destroy(route('users.destroy', idDelete), {
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
            <h1 className="text-muted">Users</h1>
            <ToastContainer />

            <div className="card">
                <TableHeader
                    searchValue={query}
                    onSearchChange={setQuery}
                    searchPlaceholder="Search by name or email..."
                    addButtonText="Add User"
                    addButtonRoute={route('users.create')}
                    searchColClass="col-md-6 col-12"
                    filterColClass=""
                    buttonColClass="col-md-6 col-12"
                />

                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody className="table-border-bottom-0">
                            {users.data.length > 0 ? (
                                users.data.map((user) => {
                                    const actions = [
                                        {
                                            label: 'Edit',
                                            icon: 'bx-edit-alt',
                                            href: route('users.edit', user.id),
                                        },
                                        {
                                            label: 'Delete',
                                            icon: 'bx-trash',
                                            onClick: () => showDeleteModal(user.id, user.name),
                                            className: 'text-danger',
                                        },
                                    ];

                                    return (
                                        <tr key={user.id}>
                                            <td>
                                                <i className="bx bx-user bx-sm me-3"></i>
                                                {user.name}
                                            </td>
                                            <td>{user.email}</td>
                                            <td>
                                                {user.roles?.length > 0 ? (
                                                    <div className="d-flex flex-wrap gap-1">
                                                        {user.roles.map((role) => (
                                                            <span
                                                                key={role}
                                                                className="badge bg-label-primary text-capitalize"
                                                            >
                                                                {role}
                                                            </span>
                                                        ))}
                                                    </div>
                                                ) : (
                                                    <span className="text-muted">No roles</span>
                                                )}
                                            </td>
                                            <td>{user.created_at}</td>
                                            <td>
                                                <ActionDropdown actions={actions} />
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td colSpan="5" className="text-center py-4">
                                        <div className="text-muted">
                                            <i className="bx bx-info-circle bx-sm me-2"></i>
                                            No users found
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
                                    Are you sure you want to delete user <strong>{nameDelete}</strong>?
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

            {users.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {users.from ?? 0} to {users.to ?? 0} of {users.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={users.links} query={query} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;
