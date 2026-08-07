import React, { useState } from 'react';
import { Link } from '@inertiajs/react';
import { ToastContainer } from 'react-toastify';
import { useFlashMessage } from '@/hooks/useFlashMessage';
import { useDeleteConfirmation } from '@/hooks/useDeleteConfirmation';
import TableHeader from '@/Components/TableHeader';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';
import ActionDropdown from '@/Components/ActionDropdown';
import Pagination from '@/Components/Pagination';
import { useDebouncedSearch } from '@/hooks/useSearch';

const Index = ({ searchTerm, supportInfo }) => {
    const [query, setQuery] = useState(searchTerm || "");
    useDebouncedSearch(query, "support-information");
    useFlashMessage();
    const { modalRef, itemToDelete, processing, confirmDelete, handleDelete } = useDeleteConfirmation('support-information.destroy');

    return (
        <>
            <h1 className="text-muted">Support Information</h1>
            <ToastContainer />

            <div className="card">
                <TableHeader
                    searchValue={query}
                    onSearchChange={setQuery}
                    searchPlaceholder="Search by key or value..."
                    addButtonText="Add Information"
                    addButtonRoute={route('support-information.create')}
                    searchColClass="col-md-6 col-12"
                    filterColClass=""
                    buttonColClass="col-md-6 col-12"
                />

                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead>
                            <tr>
                                <th>Key</th>
                                <th>Value</th>
                                <th>Image</th>
                                <th>Created</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody className="table-border-bottom-0">
                            {supportInfo.data && supportInfo.data.length > 0 ? (
                                supportInfo.data.map((item) => {
                                    const actions = [
                                        {
                                            label: 'Edit',
                                            icon: 'bx-edit-alt',
                                            href: route('support-information.edit', item.id)
                                        },
                                        {
                                            label: 'Delete',
                                            icon: 'bx-trash',
                                            onClick: () => confirmDelete(item.id, { name: item.key }),
                                            className: 'text-danger'
                                        }
                                    ];

                                    return (
                                        <tr key={item.id}>
                                            <td>
                                                <code className="text-primary p-2">{item.key}</code>
                                            </td>
                                            <td>
                                                <div className="text-truncate" style={{ maxWidth: '300px' }} title={item.value}>
                                                    {item.value.length > 50 ? item.value.substring(0, 50) + '...' : item.value}
                                                </div>
                                            </td>
                                            <td>
                                                {item.file_path ? (
                                                    <img
                                                        src={item.file_path}
                                                        alt={item.key}
                                                        style={{ width: '50px', height: '50px', objectFit: 'cover', borderRadius: '4px' }}
                                                    />
                                                ) : (
                                                    <span className="text-muted">-</span>
                                                )}
                                            </td>
                                            <td className="text-muted">{item.created_at}</td>
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
                                            {query ? 'No results found' : 'No support information available'}
                                        </div>
                                    </td>
                                </tr>
                            )}
                        </tbody>
                    </table>
                </div>
            </div>

            <DeleteConfirmationModal
                modalRef={modalRef}
                title="Confirm Deletion"
                message="Are you sure you want to delete this support information?"
                itemName={itemToDelete?.name}
                onConfirm={() => handleDelete()}
                processing={processing}
            />

            {supportInfo.links && supportInfo.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {supportInfo.from ?? 0} to {supportInfo.to ?? 0} of {supportInfo.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={supportInfo.links} query={query} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;
