import React, { useEffect, useRef, useState } from 'react';
import { Link, useForm } from '@inertiajs/react';
import Pagination from '@/Components/Pagination';
import { router, usePage } from '@inertiajs/react';
import { ToastContainer, toast } from 'react-toastify';
import debounce from "lodash/debounce";

const Index = ({ sections, searchTerm, pages, pageId }) => {
    const [query, setQuery] = useState(searchTerm || "");
    const [selectedPageId, setSelectedPageId] = useState(pageId ?? null);
    const [pageSearch, setPageSearch] = useState("");
    const [pageOffset, setPageOffset] = useState(0);
    const { flash } = usePage().props;
    const modalRef = useRef(null);
    const modalInstance = useRef(null);
    const [idDelete, setIdDelete] = useState(null);
    const { delete: destroy, processing } = useForm();

    useEffect(() => {
        if (flash.success) toast.success(flash.success);
        if (flash.error) toast.error(flash.error);
    }, [flash]);

    useEffect(() => {
        setSelectedPageId(pageId ?? null);
    }, [pageId]);

    const filteredPages = pages.filter((page) =>
        page.title.toLowerCase().includes(pageSearch.toLowerCase())
    );

    const pagesPerSlide = 5;
    const maxPageOffset = Math.max(0, filteredPages.length - pagesPerSlide);
    const visiblePages = filteredPages.slice(pageOffset, pageOffset + pagesPerSlide);

    useEffect(() => {
        if (pageOffset > maxPageOffset) {
            setPageOffset(maxPageOffset);
        }
    }, [maxPageOffset, pageOffset]);

    useEffect(() => {
        const delaySearch = debounce(() => {
            router.get(
                "page-sections",
                {
                    search: query || undefined,
                    page_id: selectedPageId || undefined,
                },
                { preserveState: true, replace: true }
            );
        }, 300);

        delaySearch();
        return () => delaySearch.cancel();
    }, [query, selectedPageId]);

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
        destroy(route('page-sections.destroy', idDelete), {
            preserveScroll: true,
            onSuccess: () => {
                modalInstance.current.hide();
                setIdDelete(null);
            }
        });
    };

    const toggleStatus = (section) => {
        router.put(route('page-sections.toggle-status', section.id), {}, {
            preserveScroll: true,
            preserveState: true,
        });
    };

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Page Sections</h1>
                    <p className="text-muted mb-0">Manage reusable page sections with dynamic fields</p>
                </div>
                <Link
                    href={route('page-sections.create')}
                    className="btn btn-primary"
                >
                    <i className='bx bx-plus me-2'></i>
                    Create Section
                </Link>
            </div>

            <ToastContainer />
            
            <div className="card">
                <div className="card-header">
                    <div className="row align-items-center">
                        <div className="col-md-6">
                            <div className="input-group input-group-merge">
                                <span className="input-group-text">
                                    <i className="bx bx-search"></i>
                                </span>
                                <input
                                    type="text"
                                    className="form-control"
                                    placeholder="Search sections by name or identifier..."
                                    value={query}
                                    onChange={(e) => setQuery(e.target.value)}
                                />
                            </div>
                        </div>
                        <div className="col-md-6 text-end d-flex justify-content-end align-items-center gap-2">
                            {query && (
                                <button
                                    onClick={() => setQuery("")}
                                    className="btn btn-sm btn-outline-secondary"
                                >
                                    Clear Search
                                </button>
                            )}

                            <div className="dropdown">
                                <button
                                    className="btn btn-sm btn-outline-secondary dropdown-toggle"
                                    type="button"
                                    id="pageFilterDropdown"
                                    data-bs-toggle="dropdown"
                                    data-bs-auto-close="outside"
                                    aria-expanded="false"
                                >
                                    {selectedPageId
                                        ? pages.find((page) => page.id === selectedPageId)?.title || 'Selected page'
                                        : 'Filter by page'}
                                </button>
                                <ul
                                    className="dropdown-menu dropdown-menu-end p-2"
                                    aria-labelledby="pageFilterDropdown"
                                    style={{ minWidth: '320px', maxWidth: '420px' }}
                                >
                                    <li className="px-2 pb-2">
                                        <input
                                            type="text"
                                            className="form-control form-control-sm"
                                            placeholder="Search pages..."
                                            value={pageSearch}
                                            onChange={(e) => {
                                                setPageSearch(e.target.value);
                                                setPageOffset(0);
                                            }}
                                            onClick={(e) => e.stopPropagation()}
                                        />
                                    </li>

                                    <li><hr className="dropdown-divider" /></li>

                                    {visiblePages.length > 0 ? (
                                        visiblePages.map((page) => (
                                            <li key={page.id}>
                                                <button
                                                    type="button"
                                                    className={`dropdown-item ${selectedPageId === page.id ? 'active' : ''}`}
                                                    onClick={() => setSelectedPageId(page.id)}
                                                >
                                                    {page.title}
                                                </button>
                                            </li>
                                        ))
                                    ) : (
                                        <li>
                                            <span className="dropdown-item-text text-muted">No pages match the search.</span>
                                        </li>
                                    )}

                                    {filteredPages.length > pagesPerSlide && (
                                        <>
                                            <li><hr className="dropdown-divider" /></li>
                                            <li className="px-2">
                                                <input
                                                    type="range"
                                                    className="form-range"
                                                    min="0"
                                                    max={maxPageOffset}
                                                    value={pageOffset}
                                                    onChange={(e) => setPageOffset(Number(e.target.value))}
                                                />
                                                <div className="d-flex justify-content-between small text-muted">
                                                    <span>Showing {pageOffset + 1}–{Math.min(pageOffset + pagesPerSlide, filteredPages.length)} of {filteredPages.length}</span>
                                                    <span>Page slide</span>
                                                </div>
                                            </li>
                                        </>
                                    )}

                                    {selectedPageId && (
                                        <>
                                            <li><hr className="dropdown-divider" /></li>
                                            <li>
                                                <button
                                                    type="button"
                                                    className="dropdown-item text-danger"
                                                    onClick={() => setSelectedPageId(null)}
                                                >
                                                    Clear page filter
                                                </button>
                                            </li>
                                        </>
                                    )}
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="table-responsive">
                    <table className="table table-hover">
                        <thead className="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Identifier</th>
                                <th>Template Preview</th>
                                <th>Fields</th>
                                <th>Used In</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th width="120">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {sections.data.length > 0 ? (
                                sections.data.map((section) => (
                                    <tr key={section.id}>
                                        <td>
                                            <strong>{section.name}</strong>
                                        </td>
                                        <td>
                                            <code className="text-primary">{section.identifier}</code>
                                        </td>
                                        <td>
                                            <small className="text-muted">
                                                {section.html_template}
                                            </small>
                                        </td>
                                        <td>
                                            <span className="badge bg-secondary">{section.fields_count} Fields</span>
                                        </td>
                                        <td>
                                            <span className="badge bg-secondary">{section.used_in_pages} Pages</span>
                                        </td>
                                        <td>
                                            <span 
                                                className={`badge ${section.is_active ? 'bg-label-success' : 'bg-label-danger'} cursor-pointer`}
                                                onClick={() => toggleStatus(section)}
                                                title="Click to toggle status"
                                            >
                                                {section.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td>
                                            <small className="text-muted">{section.created_at}</small>
                                        </td>
                                        <td>
                                            <div className="d-flex align-items-center gap-1">
                                                <div className="dropdown">
                                                    <button
                                                        aria-label='Click me'
                                                        type="button"
                                                        className="btn btn-outline-secondary p-1 m-1 dropdown-toggle hide-arrow"
                                                        data-bs-toggle="dropdown"
                                                    >
                                                        <i className="bx bx-dots-vertical-rounded"></i>
                                                    </button>
                                                    <div className="dropdown-menu">
                                                        <Link
                                                            aria-label="dropdown action option"
                                                            className="dropdown-item"
                                                            href={route('page-sections.edit', section.id)}
                                                        >
                                                            <i className="bx bx-edit-alt me-1"></i> Edit
                                                        </Link>
                                                        <Link
                                                            aria-label="dropdown action option"
                                                            className="dropdown-item"
                                                            href={route('page-sections.show', section.id)}
                                                        >
                                                            <i className="bx bx-show me-1"></i> Show
                                                        </Link>
                                                        <button
                                                            onClick={() => showDeleteModal(section.id)}
                                                            aria-label="dropdown action option"
                                                            className="dropdown-item"
                                                            disabled={processing}
                                                        >
                                                            <i className="bx bx-trash me-1"></i> Delete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            ) : (
                                <tr>
                                    <td colSpan="8" className="text-center py-4">
                                        <div className="text-muted">
                                            <i className="bx bx-inbox bx-lg mb-2"></i>
                                            <p>No page sections found</p>
                                            {query && (
                                                <small>Try adjusting your search criteria</small>
                                            )}
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
                                    Are you sure you want to delete this page section? 
                                    This action cannot be undone.
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
                                    {processing ? (
                                        <>
                                            <span className="spinner-border spinner-border-sm me-2" />
                                            Deleting...
                                        </>
                                    ) : (
                                        'Yes, Delete'
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Pagination */}
            {sections.links.length > 3 && (
                <div className="row mt-4">
                    <div className="col-md-6">
                        <p className="text-muted mb-0">
                            Showing {sections.from ?? 0} to {sections.to ?? 0} of {sections.total} entries
                        </p>
                    </div>
                    <div className="col-md-6">
                        <div className="float-end">
                            <Pagination links={sections.links} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;