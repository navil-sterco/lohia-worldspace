import React, { useEffect, useRef, useState } from "react";
import { Link, useForm } from "@inertiajs/react";
import { ToastContainer } from "react-toastify";
import { useFlashMessage } from "@/hooks/useFlashMessage";
import ActionDropdown from "@/Components/ActionDropdown";
import Pagination from "@/Components/Pagination";

const Index = ({ roles }) => {
    useFlashMessage();
    const modalRef = useRef(null);
    const modalInstance = useRef(null);
    const [idDelete, setIdDelete] = useState(null);
    const [nameDelete, setNameDelete] = useState("");
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
        destroy(route("roles.destroy", idDelete), {
            preserveScroll: true,
            onSuccess: () => {
                modalInstance.current?.hide();
                setIdDelete(null);
                setNameDelete("");
            },
        });
    };

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Roles</h1>
                    <p className="text-muted mb-0">
                        Manage roles and assign permissions
                    </p>
                </div>
                <Link href={route("roles.create")} className="btn btn-primary">
                    <i className="bx bx-plus me-2"></i>
                    Add Role
                </Link>
            </div>

            <ToastContainer />

            <div className="card">
                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead>
                            <tr>
                                <th>Role</th>
                                <th>Permissions</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody className="table-border-bottom-0">
                            {roles.data.length > 0 ? (
                                roles.data.map((role) => {
                                    const actions = [
                                        {
                                            label: "Edit",
                                            icon: "bx-edit-alt",
                                            href: route("roles.edit", role.id),
                                        },
                                        {
                                            label: "Delete",
                                            icon: "bx-trash",
                                            onClick: () =>
                                                showDeleteModal(
                                                    role.id,
                                                    role.name,
                                                ),
                                            className: "text-danger",
                                        },
                                    ];

                                    return (
                                        <tr key={role.id}>
                                            <td>
                                                <i className="bx bx-shield bx-sm me-3"></i>
                                                <span className="text-capitalize">
                                                    {role.name}
                                                </span>
                                            </td>
                                            <td>
                                                {role.permissions_count > 0 ? (
                                                    <div className="d-flex flex-wrap gap-1">
                                                        {role.name !=
                                                        "Super Admin" ? (
                                                            role.permissions.map(
                                                                (
                                                                    permission,
                                                                ) => (
                                                                    <span
                                                                        key={
                                                                            permission
                                                                        }
                                                                        className="badge bg-label-secondary"
                                                                    >
                                                                        {
                                                                            permission
                                                                        }
                                                                    </span>
                                                                ),
                                                            )
                                                        ) : (
                                                            <span
                                                                className="badge bg-label-primary"
                                                            >
                                                                All
                                                            </span>
                                                        )}
                                                    </div>
                                                ) : (
                                                    <span className="text-muted">
                                                        No permissions
                                                    </span>
                                                )}
                                            </td>
                                            <td>
                                                <ActionDropdown
                                                    actions={actions}
                                                />
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td
                                        colSpan="3"
                                        className="text-center py-4"
                                    >
                                        <div className="text-muted">
                                            <i className="bx bx-info-circle bx-sm me-2"></i>
                                            No roles found
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
                                <h5 className="modal-title">
                                    Confirm Deletion
                                </h5>
                                <button
                                    type="button"
                                    className="btn-close"
                                    data-bs-dismiss="modal"
                                ></button>
                            </div>
                            <div className="modal-body">
                                <p>
                                    Are you sure you want to delete the role{" "}
                                    <strong>{nameDelete}</strong>?
                                </p>
                            </div>
                            <div className="modal-footer">
                                <button
                                    type="button"
                                    className="btn btn-secondary"
                                    data-bs-dismiss="modal"
                                >
                                    Cancel
                                </button>
                                <button
                                    type="button"
                                    className="btn btn-danger"
                                    onClick={handleConfirmDelete}
                                    disabled={processing}
                                >
                                    {processing ? "Deleting..." : "Yes, Delete"}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {roles.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {roles.from ?? 0} to {roles.to ?? 0} of{" "}
                            {roles.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={roles.links} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;
