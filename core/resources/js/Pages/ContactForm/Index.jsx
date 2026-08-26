import React, { useRef, useState } from 'react';
import { ToastContainer } from 'react-toastify';
import { useFlashMessage } from '@/hooks/useFlashMessage';
import { useDeleteConfirmation } from '@/hooks/useDeleteConfirmation';
import TableHeader from '@/Components/TableHeader';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';
import Pagination from '@/Components/Pagination';
import { useDebouncedSearch } from '@/hooks/useSearch';

const formatLabel = (key) =>
    key
        .replace(/^vendor_/, '')
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (c) => c.toUpperCase());

const formatInterest = (interest) =>
    interest ? interest.replace(/_/g, ' ').replace(/\b\w/g, (c) => c.toUpperCase()) : '—';


const Index = ({ searchTerm, contactForms, interestLabels = {}, buyerTypeLabels = {}, budgetLabels = {}, propertyTypeLabels = {} }) => {
    
    const [query, setQuery] = useState(searchTerm || "");
    const viewModalRef = useRef(null);
    const viewModalInstance = useRef(null);
    const [selectedContactForm, setSelectedContactForm] = useState(null);
    useDebouncedSearch(query, "contact-forms");
    useFlashMessage();

    const showViewModal = (contactForm) => {
        setSelectedContactForm(contactForm);
        setTimeout(() => {
            if (viewModalRef.current) {
                const modalInstance = new window.bootstrap.Modal(viewModalRef.current);
                viewModalInstance.current = modalInstance;
                modalInstance.show();
            }
        }, 0);
    };

    const closeViewModal = () => {
        if (viewModalInstance.current) {
            viewModalInstance.current.hide();
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 300);
        }
        setSelectedContactForm(null);
    };
    const { modalRef, itemToDelete, processing, confirmDelete, handleDelete } = useDeleteConfirmation('contact-forms.destroy');

    return (
        <>
            <h1 className="text-muted">Contact Form</h1>
            <ToastContainer />

            <div className="card">
                <TableHeader
                    searchValue={query}
                    onSearchChange={setQuery}
                    searchPlaceholder="Search By Contact Form Name and Email..."
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
                                <th>Phone</th>
                                <th>Interest</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {contactForms.data.map((item) => (
                                <tr key={item.id}>
                                    <td>
                                        <i className="bx bx-text bx-sm me-3"></i>
                                        {item.name}
                                    </td>
                                    <td>
                                        <i className="bx bx-envelope bx-sm me-3"></i>
                                        {item.email}
                                    </td>
                                    <td>
                                        <i className="bx bx-phone bx-sm me-3"></i>
                                        {item.phone !== '' ? item.phone : <span className="text-muted">—</span>}
                                    </td>
                                    <td>
                                        <span className="badge bg-label-primary">
                                            {interestLabels[item.interest] || formatInterest(item.interest)}


                                        </span>
                                    </td>
                                    <td>
                                        <div className="d-flex align-items-center gap-1">
                                            <div className="dropdown">
                                                <button
                                                    className="btn btn-outline-secondary p-1 dropdown-toggle hide-arrow"
                                                    data-bs-toggle="dropdown"
                                                >
                                                    <i className="bx bx-dots-vertical-rounded"></i>
                                                </button>
                                                <div className="dropdown-menu">


                                                    <a href="#viewDetailsModal"
                                                        data-bs-toggle="modal"
                                                        onClick={() => showViewModal(item)}
                                                        className="dropdown-item" >
                                                        <i className="bx bx-show me-1"></i> View
                                                    </a>

                                                    <a onClick={() => confirmDelete(item.id, { name: item.name })}
                                                        className="dropdown-item"
                                                        href="#" >
                                                        <i className="bx bx-trash me-1"></i> Delete
                                                    </a>
                                                </div>
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
                message="Are you sure you want to delete this form data?"
                itemName={itemToDelete?.name}
                onConfirm={() => handleDelete()}
                processing={processing}
            />

            {/* View Sidebar Modal */}
            <div
                className="modal fade modal-right"
                id="viewDetailsModal"
                tabIndex="-1"
                aria-hidden="true"
                ref={viewModalRef}
            >
                <div className="modal-dialog modal-dialog-scrollable modal-xl">
                    <div className="modal-content h-100">
                        <div className="modal-header bg-light">
                            <div className="d-flex align-items-center w-100">
                                <div className="flex-grow-1">
                                    <h5 className="modal-title fw-semibold text-primary">
                                        <i className="bx bx-search-alt me-2"></i>
                                        Contact Form Details
                                    </h5>
                                    {selectedContactForm && (
                                        <p className="text-muted mb-0 small">
                                            ID: {selectedContactForm.id} • Created: {selectedContactForm.created_at}
                                        </p>
                                    )}
                                </div>
                                <button
                                    type="button"
                                    className="btn-close"
                                    onClick={closeViewModal}
                                ></button>
                            </div>
                        </div>

                        {/* Body */}
                        <div className="modal-body p-0">
                            {selectedContactForm ? (
                                <div className="row g-0">
                                    <div className="col-md-8 p-4 border-end">
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-user me-2"></i>
                                                Contact Information
                                            </h6>
                                            <div className="row g-3">
                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">Interest / Purpose</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <span className="badge bg-primary">
                                                            {interestLabels[selectedContactForm.interest] || formatInterest(selectedContactForm.interest)}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">Full Name</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <div className="d-flex align-items-start">
                                                            <i className="bx bx-user text-primary me-2 mt-1"></i>
                                                            <span className="fw-medium">{selectedContactForm.name || <span className="text-muted">—</span>}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">Email Address</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <div className="d-flex align-items-start">
                                                            <i className="bx bx-envelope text-primary me-2 mt-1"></i>
                                                            <span className="fw-medium">
                                                                <a href={`mailto:${selectedContactForm.email}`} className="text-decoration-none text-primary">
                                                                    {selectedContactForm.email || <span className="text-muted">—</span>}
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="col-md-6">
                                                    <label className="form-label fw-semibold text-muted small">Phone Number</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <div className="d-flex align-items-start">
                                                            <i className="bx bx-phone text-primary me-2 mt-1"></i>
                                                            <span className="fw-medium">
                                                                <a href={`tel:${selectedContactForm.phone}`} className="text-decoration-none text-primary">
                                                                    {selectedContactForm.phone || <span className="text-muted">—</span>}
                                                                </a>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div className="col-md-6">
                                                    <label className="form-label fw-semibold text-muted small">Location</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <div className="d-flex align-items-start">
                                                            <i className="bx bx-map-pin text-primary me-2 mt-1"></i>
                                                            <span className="fw-medium">{selectedContactForm.location || <span className="text-muted">—</span>}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {(selectedContactForm.buyer_type || selectedContactForm.budget || selectedContactForm.property_type) && (
                                            <div className="mb-4">
                                                <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                    <i className="bx bx-home-circle me-2"></i>
                                                    Property Preferences
                                                </h6>
                                                <div className="row g-3">
                                                    <div className="col-md-4">
                                                        <label className="form-label fw-semibold text-muted small">Buyer Type</label>
                                                        <div className="border rounded p-3 bg-light">
                                                            <span className="fw-medium">
                                                                {buyerTypeLabels[selectedContactForm.buyer_type] || selectedContactForm.buyer_type || <span className="text-muted">—</span>}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-4">
                                                        <label className="form-label fw-semibold text-muted small">Budget</label>
                                                        <div className="border rounded p-3 bg-light">
                                                            <span className="fw-medium">
                                                                {budgetLabels[selectedContactForm.budget] || selectedContactForm.budget || <span className="text-muted">—</span>}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div className="col-md-4">
                                                        <label className="form-label fw-semibold text-muted small">Property Type</label>
                                                        <div className="border rounded p-3 bg-light">
                                                            <span className="fw-medium">
                                                                {propertyTypeLabels[selectedContactForm.property_type] || selectedContactForm.property_type || <span className="text-muted">—</span>}
                                                            </span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        )}

                                        {/* Dynamically render every field in `details` JSON —
                                            covers all vendor_* fields, or any other section-specific data */}
                                        {selectedContactForm.details && Object.keys(selectedContactForm.details).length > 0 && (
                                            <div className="mb-4">
                                                <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                    <i className="bx bx-detail me-2"></i>
                                                    {selectedContactForm.interest === 'vendor' ? 'Vendor Details' : 'Additional Details'}
                                                </h6>
                                                <div className="row g-3">
                                                    {Object.entries(selectedContactForm.details).map(([key, value]) => (
                                                        <div className="col-md-6" key={key}>
                                                            <label className="form-label fw-semibold text-muted small">
                                                                {formatLabel(key)}
                                                            </label>
                                                            <div className="border rounded p-3 bg-light">
                                                                <span className="fw-medium">
                                                                    {value !== null && value !== '' ? String(value) : <span className="text-muted">—</span>}
                                                                </span>
                                                            </div>
                                                        </div>
                                                    ))}
                                                </div>
                                            </div>
                                        )}

                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-message-square me-2"></i>
                                                Message & Details
                                            </h6>
                                            <div className="row g-3">
                                                {selectedContactForm.comment && (
                                                    <div className="col-12">
                                                        <label className="form-label fw-semibold text-muted small">Comment</label>
                                                        <div className="border rounded p-3 bg-light">
                                                            <p className="mb-0 small">{selectedContactForm.comment}</p>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </div>

                                    {/* Sidebar - Right Side */}
                                    <div className="col-md-4 p-4 bg-light">
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-info-circle me-2"></i>
                                                Submission Details
                                            </h6>
                                            <div className="vstack gap-3">
                                                <div>
                                                    <label className="form-label fw-semibold text-muted small mb-2">Reference ID</label>
                                                    <div className="p-2 bg-white rounded border">
                                                        <code className="text-primary small">{selectedContactForm.id || '—'}</code>
                                                    </div>
                                                </div>
                                                {selectedContactForm.ip_address && (
                                                    <div>
                                                        <label className="form-label fw-semibold text-muted small mb-2">IP Address</label>
                                                        <div className="p-2 bg-white rounded border">
                                                            <code className="text-muted small">{selectedContactForm.ip_address}</code>
                                                        </div>
                                                    </div>
                                                )}
                                                <div>
                                                    <label className="form-label fw-semibold text-muted small mb-2">Submitted On</label>
                                                    <div className="p-2 bg-white rounded border">
                                                        <small className="text-muted">
                                                            <i className="bx bx-calendar me-1"></i>
                                                            {selectedContactForm.created_at
                                                                ? new Date(selectedContactForm.created_at).toLocaleString()
                                                                : '—'}
                                                        </small>
                                                    </div>
                                                </div>

                                                {selectedContactForm.updated_at && (
                                                    <div>
                                                        <label className="form-label fw-semibold text-muted small mb-2">Last Updated</label>
                                                        <div className="p-2 bg-white rounded border">
                                                            <small className="text-muted">
                                                                <i className="bx bx-refresh me-1"></i>
                                                                {new Date(selectedContactForm.updated_at).toLocaleString()}
                                                            </small>
                                                        </div>
                                                    </div>
                                                )}
                                            </div>
                                        </div>

                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-cog me-2"></i>
                                                Quick Actions
                                            </h6>
                                            <div className="space-y-3">
                                                <div className="d-grid gap-2">
                                                    <button
                                                        onClick={() => confirmDelete(selectedContactForm.id, { name: selectedContactForm.name })}
                                                        className="btn btn-outline-danger btn-sm"
                                                    >
                                                        <i className="bx bx-trash me-1"></i>
                                                        Delete
                                                    </button>
                                                    <button
                                                        onClick={closeViewModal}
                                                        className="btn btn-secondary btn-sm"
                                                    >
                                                        <i className="bx bx-x me-1"></i>
                                                        Close
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        <div>
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-stats me-2"></i>
                                                Field Status
                                            </h6>
                                            <div className="space-y-2">
                                                <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-1">
                                                    <span className="small">Name</span>
                                                    <span className="badge bg-success">
                                                        <i className="bx bx-check"></i>
                                                    </span>
                                                </div>
                                                <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-1">
                                                    <span className="small">Email</span>
                                                    <span className="badge bg-success">
                                                        <i className="bx bx-check"></i>
                                                    </span>
                                                </div>
                                                <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-1">
                                                    <span className="small">Phone</span>
                                                    <span className={`badge ${selectedContactForm.phone ? 'bg-success' : 'bg-secondary'}`}>
                                                        <i className={`bx ${selectedContactForm.phone ? 'bx-check' : 'bx-x'}`}></i>
                                                    </span>
                                                </div>
                                                <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded border">
                                                    <span className="small">Comment</span>
                                                    <span className={`badge ${selectedContactForm.comment ? 'bg-success' : 'bg-secondary'}`}>
                                                        <i className={`bx ${selectedContactForm.comment ? 'bx-check' : 'bx-x'}`}></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            ) : (
                                <div className="text-center py-5">
                                    <i className="bx bx-error-circle text-muted mb-3" style={{ fontSize: "3rem" }}></i>
                                    <p className="text-muted">No details available.</p>
                                </div>
                            )}
                        </div>

                        <div className="modal-footer bg-light">
                            <button
                                type="button"
                                className="btn btn-secondary"
                                onClick={closeViewModal}
                            >
                                <i className="bx bx-x me-1"></i>
                                Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {contactForms.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {contactForms.from ?? 0} to {contactForms.to ?? 0} of {contactForms.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={contactForms.links} query={query} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;
