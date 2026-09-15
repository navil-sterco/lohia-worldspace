import React, { useState } from 'react';
import { ToastContainer } from 'react-toastify';
import { useFlashMessage } from '@/hooks/useFlashMessage';
import { useDeleteConfirmation } from '@/hooks/useDeleteConfirmation';
import TableHeader from '@/Components/TableHeader';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';
import Pagination from '@/Components/Pagination';
import { useDebouncedSearch } from '@/hooks/useSearch';

const formatLabel = (key) => key.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase());
const displayValue = (value) => value !== null && value !== undefined && value !== '' ? String(value) : '—';

export default function SubmissionIndex({ title, searchPlaceholder, searchTerm, submissions, routePrefix, columns, fields }) {
    const [query, setQuery] = useState(searchTerm || '');
    const [selected, setSelected] = useState(null);
    useDebouncedSearch(query, routePrefix);
    useFlashMessage();

    const { modalRef, itemToDelete, processing, confirmDelete, handleDelete } = useDeleteConfirmation(`${routePrefix}.destroy`);

    return (
        <>
            <h1 className="text-muted">{title}</h1>
            <ToastContainer />
            <div className="card">
                <TableHeader
                    searchValue={query}
                    onSearchChange={setQuery}
                    searchPlaceholder={searchPlaceholder}
                    searchColClass="col-md-6 col-12"
                    filterColClass=""
                    buttonColClass="col-md-6 col-12"
                />
                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead><tr>{columns.map((column) => <th key={column.key}>{column.label}</th>)}<th>Actions</th></tr></thead>
                        <tbody>
                            {submissions.data.map((item) => (
                                <tr key={item.id}>
                                    {columns.map((column) => <td key={column.key}>{displayValue(item[column.key])}</td>)}
                                    <td>
                                        <div className="dropdown">
                                            <button className="btn btn-outline-secondary p-1 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                                <i className="bx bx-dots-vertical-rounded"></i>
                                            </button>
                                            <div className="dropdown-menu">
                                                <button className="dropdown-item" onClick={() => setSelected(item)} data-bs-toggle="modal" data-bs-target="#submissionDetailsModal">
                                                    <i className="bx bx-show me-1"></i> View
                                                </button>
                                                <button className="dropdown-item" onClick={() => confirmDelete(item.id, { name: item.name || item.email })}>
                                                    <i className="bx bx-trash me-1"></i> Delete
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>

            <DeleteConfirmationModal
                modalRef={modalRef}
                title="Confirm Deletion"
                message="Are you sure you want to delete this submission?"
                itemName={itemToDelete?.name || itemToDelete?.email}
                onConfirm={() => handleDelete()}
                processing={processing}
            />

            <div className="modal fade" id="submissionDetailsModal" tabIndex="-1" aria-hidden="true">
                <div className="modal-dialog modal-dialog-scrollable modal-xl">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title"><i className="bx bx-detail me-2"></i>{title} Details</h5>
                            <button type="button" className="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div className="modal-body">
                            {selected && (
                                <div className="row g-3">
                                    {fields.map((field) => {
                                        const value = selected[field.key];
                                        const isFile = field.file && value;
                                        return (
                                            <div className={field.wide ? 'col-12' : 'col-md-6'} key={field.key}>
                                                <label className="form-label fw-semibold text-muted small">{field.label || formatLabel(field.key)}</label>
                                                <div className="border rounded p-3 bg-light" style={{ whiteSpace: 'pre-wrap', overflowWrap: 'anywhere' }}>
                                                    {isFile ? <a href={`/${String(value).replace(/^\/+/, '')}`} target="_blank" rel="noreferrer">{selected[field.nameKey] || 'Open attachment'}</a> : displayValue(value)}
                                                </div>
                                            </div>
                                        );
                                    })}
                                </div>
                            )}
                        </div>
                        <div className="modal-footer"><button type="button" className="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
                    </div>
                </div>
            </div>

            {submissions.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4"><p className="text-dark mb-0 mt-2">Showing {submissions.from ?? 0} to {submissions.to ?? 0} of {submissions.total} entries</p></div>
                    <div className="col-md-8"><div className="float-end"><Pagination links={submissions.links} query={query} /></div></div>
                </div>
            )}
        </>
    );
}
