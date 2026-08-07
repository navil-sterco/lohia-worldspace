import React, { useEffect, useRef, useState } from "react";
import { debounce } from "lodash";
import { Link, useForm } from "@inertiajs/react";
import Pagination from "@/Components/Pagination";
import TableHeader from '@/Components/TableHeader';
import { router, usePage } from "@inertiajs/react";
import { ToastContainer, toast } from "react-toastify";
import { useDeleteConfirmation } from '@/hooks/useDeleteConfirmation';
import DeleteConfirmationModal from '@/Components/DeleteConfirmationModal';

const SeoIndex = (props) => {
    const { seo, searchTerm } = props;
    const [query, setQuery] = useState(searchTerm || "");
    const { flash } = usePage().props;
    const [selectedSeo, setSelectedSeo] = useState(null);
    const viewModalRef = useRef(null);
    const viewModalInstance = useRef(null);
    const [selectedImage, setSelectedImage] = useState(null);
    const imageModalRef = useRef(null);
    const imageModalInstance = useRef(null);

    const { processing } = useForm();

    useEffect(() => {
        if (flash.success) {
            toast.success(flash.success);
        }
    }, [flash.success]);

    useEffect(() => {
        const delaySearch = debounce(() => {
            router.get("seo", { search: query }, { preserveState: true, replace: true });
        }, 300);

        delaySearch();
        return () => delaySearch.cancel();
    }, [query]);

    const { modalRef, itemToDelete,confirmDelete, handleDelete } = useDeleteConfirmation('seo.destroy');

    const showImageModal = (imageUrl) => {
        setSelectedImage(imageUrl);
        setTimeout(() => {
            if (imageModalRef.current) {
                const modalInstance = new window.bootstrap.Modal(imageModalRef.current);
                imageModalInstance.current = modalInstance;
                modalInstance.show();
            }
        }, 0);
    };

    const showViewModal = (seo) => {
        setSelectedSeo(seo);
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
            // Remove backdrop after modal hide animation completes
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 300);
        }
        setSelectedSeo(null);
    };

    const closeImageModal = () => {
        if (imageModalInstance.current) {
            imageModalInstance.current.hide();
            // Remove backdrop after modal hide animation completes
            setTimeout(() => {
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
            }, 300);
        }
        setSelectedImage(null);
    };

    const handleEditFromModal = (id) => {
        closeViewModal();
        setTimeout(() => {
            router.get(route("seo.edit", id));
        }, 150);
    };

    const truncateText = (text, length = 50) => {
        if (!text) return "—";
        return text.length > length ? text.substring(0, length) + "..." : text;
    };

    return (
        <>
            <h1 className="text-muted">SEO List</h1>
            <ToastContainer />

            <div className="card">
                <TableHeader
                    searchValue={query}
                    onSearchChange={setQuery}
                    searchPlaceholder="Search By SEO Title..."
                    addButtonText="Add SEO"
                    addButtonRoute={route('seo.create')}
                    searchColClass="col-md-6 col-12"
                    filterColClass=""
                    buttonColClass="col-md-6 col-12"
                />

                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead>
                            <tr>
                                <th>Meta Title</th>
                                <th>Url</th>
                                <th>OG Type</th>
                                <th>OG Image</th>
                                <th>Canonical URL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {seo.data.map((item) => (
                                <tr key={item.id}>
                                    <td>
                                        <i className="bx bx-text bx-sm me-3"></i>
                                        {truncateText(item.meta_title)}
                                    </td>
                                    <td>
                                        <i className="bx bx-link bx-sm me-3"></i>
                                        {truncateText(item.url)}
                                    </td>
                                    <td>
                                        <span className="badge bg-label-primary">{item.og_type}</span>
                                    </td>
                                    <td>
                                        {item.og_image ? (
                                            <img
                                                src={item.og_image}
                                                alt="OG Image"
                                                className="img-thumbnail"
                                                style={{
                                                    width: "80px",
                                                    height: "50px",
                                                    objectFit: "cover",
                                                    cursor: "pointer",
                                                }}
                                                onClick={() => showImageModal(item.og_image)}
                                            />
                                        ) : (
                                            <span className="text-muted">No image</span>
                                        )}
                                    </td>
                                    <td>
                                        <i className="bx bx-globe bx-sm me-3"></i>
                                        {truncateText(item.canonical_url)}
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
                                                    <a
                                                        href="#viewDetailsModal"
                                                        data-bs-toggle="modal"
                                                        onClick={() => showViewModal(item)}
                                                        className="dropdown-item"
                                                    >
                                                        <i className="bx bx-show me-1"></i> View
                                                    </a>
                                                    <Link
                                                        className="dropdown-item"
                                                        href={route("seo.edit", item.id)}
                                                    >
                                                        <i className="bx bx-edit-alt me-1"></i> Edit
                                                    </Link>
                                                    <a
                                                        onClick={() => confirmDelete(item.id, { name: item.name })}
                                                        className="dropdown-item"
                                                        href="#"
                                                    >
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
                message="Are you sure you want to delete this Seo Meta?"
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
                        {/* Header */}
                        <div className="modal-header bg-light">
                            <div className="d-flex align-items-center w-100">
                                <div className="flex-grow-1">
                                    <h5 className="modal-title fw-semibold text-primary">
                                        <i className="bx bx-search-alt me-2"></i>
                                        SEO Details
                                    </h5>
                                    {selectedSeo && (
                                        <p className="text-muted mb-0 small">
                                            ID: {selectedSeo.id} • Created: {selectedSeo.created_at}
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
                            {selectedSeo ? (
                                <div className="row g-0">
                                    {/* Main Content - Left Side */}
                                    <div className="col-md-8 p-4 border-end">
                                        {/* Basic SEO Information */}
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-info-circle me-2"></i>
                                                Basic SEO Information
                                            </h6>
                                            <div className="row g-3">
                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">Meta Title</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <div className="d-flex align-items-start">
                                                            <i className="bx bx-text text-primary me-2"></i>
                                                            <span className="fw-medium">{selectedSeo.meta_title || <span className="text-muted">—</span>}</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">Meta Description</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <p className="mb-0 small">{selectedSeo.meta_description || <span className="text-muted">—</span>}</p>
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <label className="form-label fw-semibold text-muted small">Url</label>
                                                    <div className="d-flex align-items-center">
                                                        <i className="bx bx-link text-primary me-2"></i>
                                                        <span className="fw-medium">{selectedSeo.url}</span>
                                                    </div>
                                                </div>
                                                <div className="col-md-6">
                                                    <label className="form-label fw-semibold text-muted small">OG Type</label>
                                                    <div>
                                                        <span className="badge bg-primary">
                                                            <i className="bx bx-tag me-1"></i>
                                                            {selectedSeo.og_type}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Open Graph Information */}
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-share-alt me-2"></i>
                                                Open Graph Information
                                            </h6>
                                            <div className="row g-3">
                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">OG Title</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <p className="mb-0 small">{selectedSeo.og_title || <span className="text-muted">—</span>}</p>
                                                    </div>
                                                </div>
                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">OG Description</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <p className="mb-0 small">{selectedSeo.og_description || <span className="text-muted">—</span>}</p>
                                                    </div>
                                                </div>
                                                <div className="col-12">
                                                    <label className="form-label fw-semibold text-muted small">OG URL</label>
                                                    <div className="border rounded p-3 bg-light">
                                                        <p className="mb-0 small">{selectedSeo.og_url || <span className="text-muted">—</span>}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Canonical URL */}
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-globe me-2"></i>
                                                Canonical Information
                                            </h6>
                                            <div className="border rounded p-3 bg-light">
                                                <div className="d-flex align-items-start">
                                                    <i className="bx bx-link-alt text-primary me-2"></i>
                                                    <div>
                                                        <p className="mb-1 fw-semibold">Canonical URL</p>
                                                        <p className="mb-0 small text-break">{selectedSeo.canonical_url || <span className="text-muted">Not specified</span>}</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {/* Sidebar - Right Side */}
                                    <div className="col-md-4 p-4 bg-light">
                                        {/* Keywords Section - ADDED THIS */}
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-key me-2"></i>
                                                Keywords
                                            </h6>
                                            {selectedSeo.keywords && selectedSeo.keywords.length > 0 ? (
                                                <div className="border rounded p-3 bg-white">
                                                    <div className="d-flex flex-wrap gap-2">
                                                        {selectedSeo.keywords.map((keyword, index) => (
                                                            <span 
                                                                key={index}
                                                                className="badge bg-primary bg-opacity-10 border border-primary border-opacity-25"
                                                            >
                                                                <i className="bx bx-hash me-1"></i>
                                                                {keyword}
                                                            </span>
                                                        ))}
                                                    </div>
                                                    <div className="mt-2 text-center">
                                                        <small className="text-muted">
                                                            {selectedSeo.keywords.length} keyword(s)
                                                        </small>
                                                    </div>
                                                </div>
                                            ) : (
                                                <div className="border rounded p-4 text-center bg-white">
                                                    <i className="bx bx-key text-muted mb-2" style={{ fontSize: "2rem" }}></i>
                                                    <p className="text-muted small mb-0">No keywords added</p>
                                                </div>
                                            )}
                                        </div>

                                        {/* OG Image Preview */}
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-image me-2"></i>
                                                Open Graph Image
                                            </h6>
                                            {selectedSeo.og_image ? (
                                                <div 
                                                    className="border rounded p-3 bg-white cursor-pointer text-center"
                                                    onClick={() => showImageModal(selectedSeo.og_image)}
                                                >
                                                    <img
                                                        src={selectedSeo.og_image}
                                                        alt="OG Image"
                                                        className="img-fluid mb-2"
                                                        style={{ width: "200px", height: "150px", objectFit: "cover" }}
                                                    />
                                                    <div className="text-center">
                                                        <small className="text-primary">
                                                            <i className="bx bx-zoom-in me-1"></i>
                                                            Click to enlarge
                                                        </small>
                                                    </div>
                                                </div>
                                            ) : (
                                                <div className="border rounded p-4 text-center bg-white">
                                                    <i className="bx bx-image text-muted mb-2" style={{ fontSize: "3rem" }}></i>
                                                    <p className="text-muted small mb-0">No OG image</p>
                                                </div>
                                            )}
                                        </div>

                                        {/* Status & Quick Actions */}
                                        <div className="mb-4">
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-cog me-2"></i>
                                                Status & Actions
                                            </h6>
                                            <div className="space-y-3">
                                                <div className="d-grid gap-2">
                                                    <button
                                                        onClick={() => handleEditFromModal(selectedSeo.id)}
                                                        className="btn btn-primary btn-sm"
                                                    >
                                                        <i className="bx bx-edit me-1"></i>
                                                        Edit SEO
                                                    </button>
                                                    <button
                                                        onClick={() => {
                                                            closeViewModal();
                                                            showDeleteModal(selectedSeo.id);
                                                        }}
                                                        className="btn btn-outline-danger btn-sm"
                                                    >
                                                        <i className="bx bx-trash me-1"></i>
                                                        Delete SEO
                                                    </button>
                                                </div>
                                            </div>
                                        </div>

                                        {/* Character Counts */}
                                        <div>
                                            <h6 className="section-title text-uppercase text-muted fw-semibold mb-3">
                                                <i className="bx bx-stats me-2"></i>
                                                Character Counts
                                            </h6>
                                            <div className="space-y-2">
                                                <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-1">
                                                    <span className="small">Meta Title</span>
                                                    <span className={`badge ${selectedSeo.meta_title?.length <= 60 ? 'bg-success' : 'bg-warning'}`}>
                                                        {selectedSeo.meta_title?.length || 0}/60
                                                    </span>
                                                </div>
                                                <div className="d-flex justify-content-between align-items-center p-2 bg-white rounded border mb-1">
                                                    <span className="small">Meta Description</span>
                                                    <span className={`badge ${selectedSeo.meta_description?.length <= 160 ? 'bg-success' : 'bg-warning'}`}>
                                                        {selectedSeo.meta_description?.length || 0}/160
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

                        {/* Footer */}
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

            {/* Image Preview Modal */}
            <div
                className="modal fade"
                id="imagePreviewModal"
                tabIndex="-1"
                aria-hidden="true"
                ref={imageModalRef}
            >
                <div className="modal-dialog modal-dialog-centered modal-lg">
                    <div className="modal-content">
                        <div className="modal-header">
                            <h5 className="modal-title">OG Image Preview</h5>
                            <button 
                                type="button" 
                                className="btn-close" 
                                onClick={closeImageModal}
                            ></button>
                        </div>
                        <div className="modal-body text-center">
                            {selectedImage ? (
                                <img
                                    src={selectedImage}
                                    alt="OG Image Preview"
                                    style={{ maxWidth: "100%", maxHeight: "80vh", borderRadius: "8px" }}
                                />
                            ) : (
                                <p>No image available</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Pagination */}
            {seo.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {seo.from ?? 0} to {seo.to ?? 0} of {seo.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={seo.links} query={query} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default SeoIndex;
