import React, { useMemo, useState } from "react";
import { Link, router } from "@inertiajs/react";
import { ToastContainer } from "react-toastify";
import { useFlashMessage } from "@/hooks/useFlashMessage";
import TableHeader from "@/Components/TableHeader";
import DeleteConfirmationModal from "@/Components/DeleteConfirmationModal";
import ActionDropdown from "@/Components/ActionDropdown";
import Pagination from "@/Components/Pagination";
import { useDebouncedSearch } from "@/hooks/useSearch";
import { useModal } from "@/hooks/useModal";
import { useCan, useModuleCan } from "@/hooks/useCan";

const Index = ({
    module,
    entries,
    searchTerm,
    pagesList = [],
    selectedPageId = null,
}) => {
    const can = useCan();
    const moduleCan = useModuleCan(module?.slug);
    const [query, setQuery] = useState(searchTerm || "");
    const [pageFilter, setPageFilter] = useState(
        selectedPageId ? String(selectedPageId) : "",
    );
    const extraParams = useMemo(
        () => (pageFilter ? { page_id: pageFilter } : {}),
        [pageFilter],
    );
    useDebouncedSearch(
        query,
        route("modules.entries.index", module.id),
        extraParams,
    );
    useFlashMessage();
    const hasSections =
        Array.isArray(module?.sections) && module.sections.length > 0;

    const fields = useMemo(
        () =>
            Array.isArray(module?.fields_config) ? module.fields_config : [],
        [module],
    );
    const listFields = useMemo(() => {
        // Filter out image, file, textarea, and code fields
        return fields
            .filter(
                (f) => !["image", "file", "textarea", "code"].includes(f.type),
            )
            .slice(0, 7);
    }, [fields]);

    const { modalRef, show, hide } = useModal();
    const [itemToDelete, setItemToDelete] = useState(null);
    const [processing, setProcessing] = useState(false);

    const confirmDelete = (entry) => {
        setItemToDelete(entry);
        show();
    };

    const handleDelete = () => {
        if (!itemToDelete?.id) return;
        setProcessing(true);
        router.delete(
            route("modules.entries.destroy", {
                module: module.id,
                entry: itemToDelete.id,
            }),
            {
                preserveScroll: true,
                onFinish: () => setProcessing(false),
                onSuccess: () => {
                    hide();
                    setItemToDelete(null);
                },
            },
        );
    };

    const togglePublish = (module, entry) => {
        router.put(
            route("modules.entries.toggle-publish", {
                module: module.id,
                entry: entry.id,
            }),
            {},
            {
                preserveScroll: true,
                preserveState: true,
            },
        );
    };

    return (
        <>
            <h1 className="text-muted">{module?.name} Entries</h1>
            <ToastContainer />

            <div className="card">
                <TableHeader
                    searchValue={query}
                    onSearchChange={setQuery}
                    searchPlaceholder={`Search ${module?.name}...`}
                    addButtonText={`Add ${module?.name}`}
                    addButtonRoute={
                        moduleCan.create
                            ? route("modules.entries.create", module.id)
                            : null
                    }
                    showAddButton={moduleCan.create}
                    searchColClass="col-md-5 col-12"
                    filterColClass="col-md-3 col-12"
                    buttonColClass="col-md-4 col-12"
                    additionalButtons={
                        can("modules.update") ? (
                            <Link
                                href={route("modules.edit", module.id)}
                                className="btn btn-outline-primary"
                            >
                                <i className="bx bx-edit me-1"></i>
                                <span className="d-none d-sm-inline-block">
                                    Edit Fields
                                </span>
                            </Link>
                        ) : null
                    }
                    filters={
                        <div className="d-flex align-items-center gap-3">
                            <select
                                className="form-select"
                                value={pageFilter}
                                onChange={(e) => {
                                    const value = e.target.value;
                                    setPageFilter(value);

                                    router.get(
                                        route(
                                            "modules.entries.index",
                                            module.id,
                                        ),
                                        {
                                            search: query,
                                            ...(value
                                                ? { page_id: value }
                                                : {}),
                                        },
                                        {
                                            preserveState: true,
                                            replace: true,
                                            preserveScroll: true,
                                        },
                                    );
                                }}
                            >
                                <option value="">All Pages</option>
                                {pagesList?.map((p) => (
                                    <option key={p.id} value={p.id}>
                                        {p.title}
                                    </option>
                                ))}
                            </select>
                        </div>
                    }
                />

                <div className="table-responsive text-nowrap">
                    <table className="table table-hover my-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                {listFields.map((f) => (
                                    <th key={f.name}>{f.label || f.name}</th>
                                ))}
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody className="table-border-bottom-0">
                            {entries.data.length > 0 ? (
                                entries.data.map((entry) => {
                                    const actions = [
                                        ...(moduleCan.subPages
                                            ? [
                                                  {
                                                      label: "Entry Pages",
                                                      icon: "bx-add-to-queue",
                                                      href: route(
                                                          "modules.entries.pages",
                                                          {
                                                              module: module.id,
                                                              entry: entry.id,
                                                          },
                                                      ),
                                                  },
                                              ]
                                            : []),
                                        ...(moduleCan.view
                                            ? [
                                                  {
                                                      label: "Show",
                                                      icon: "bx-show",
                                                      href: route(
                                                          "modules.entries.show",
                                                          {
                                                              module: module.id,
                                                              entry: entry.id,
                                                          },
                                                      ),
                                                  },
                                              ]
                                            : []),
                                        ...(moduleCan.update
                                            ? [
                                                  {
                                                      label: "Edit",
                                                      icon: "bx-edit-alt",
                                                      href: route(
                                                          "modules.entries.edit",
                                                          {
                                                              module: module.id,
                                                              entry: entry.id,
                                                          },
                                                      ),
                                                  },
                                              ]
                                            : []),
                                        ...(moduleCan.delete
                                            ? [
                                                  {
                                                      label: "Delete",
                                                      icon: "bx-trash",
                                                      onClick: () =>
                                                          confirmDelete(entry),
                                                      className: "text-danger",
                                                  },
                                              ]
                                            : []),
                                    ];

                                    return (
                                        <tr key={entry.id}>
                                            <td>
                                                <div className="d-flex align-items-center gap-2">
                                                    <div className="bg-primary bg-opacity-10 rounded p-2">
                                                        <i className="bx bx-cube text-white fs-5"></i>
                                                    </div>
                                                    <span>{entry.id}</span>
                                                </div>
                                            </td>
                                            {listFields.map((f) => (
                                                <td
                                                    key={f.name}
                                                    className="description-cell"
                                                >
                                                    {entry.data?.[f.name]
                                                        ? String(
                                                              entry.data[
                                                                  f.name
                                                              ],
                                                          )
                                                        : "N/A"}
                                                </td>
                                            ))}

                                            <td>
                                                <span
                                                    className={`badge ${entry.is_published ? "bg-label-success" : "bg-label-secondary"} cursor-pointer`}
                                                    onClick={() =>
                                                        togglePublish(
                                                            module,
                                                            entry,
                                                        )
                                                    }
                                                    title="Click to toggle publish status"
                                                >
                                                    {entry.is_published
                                                        ? "Published"
                                                        : "Draft"}
                                                </span>
                                            </td>
                                            <td>
                                                <div className="d-flex align-items-center gap-1">
                                                    {hasSections &&
                                                        moduleCan.detail && (
                                                            <Link
                                                                className="btn btn-sm btn-outline-secondary p-1"
                                                                href={route(
                                                                    "modules.entries.detail",
                                                                    {
                                                                        module: module.id,
                                                                        entry: entry.id,
                                                                    },
                                                                )}
                                                            >
                                                                <i className="bx bx-detail me-1"></i>
                                                                Detail
                                                            </Link>
                                                        )}
                                                    {moduleCan.mapping && (
                                                        <Link
                                                            className="btn btn-sm btn-outline-primary p-1"
                                                            href={route(
                                                                "modules.entries.mapping",
                                                                {
                                                                    module: module.id,
                                                                    entry: entry.id,
                                                                },
                                                            )}
                                                        >
                                                            <i className="bx bx-right-arrow-circle me-1"></i>
                                                            Mapping
                                                        </Link>
                                                    )}
                                                    {actions.length > 0 && (
                                                        <ActionDropdown
                                                            actions={actions}
                                                        />
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    );
                                })
                            ) : (
                                <tr>
                                    <td
                                        colSpan={listFields.length + 2}
                                        className="text-center py-4"
                                    >
                                        <div className="text-muted">
                                            <i className="bx bx-info-circle bx-sm me-2"></i>
                                            No entries found
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
                message={`Are you sure you want to delete this ${module?.name} entry?`}
                itemName={itemToDelete ? `Entry #${itemToDelete.id}` : null}
                onConfirm={handleDelete}
                onCancel={() => {
                    hide();
                    setItemToDelete(null);
                }}
                processing={processing}
            />

            {entries.links.length > 3 && (
                <div className="row m-2">
                    <div className="col-md-4">
                        <p className="text-dark mb-0 mt-2">
                            Showing {entries.from ?? 0} to {entries.to ?? 0} of{" "}
                            {entries.total} entries
                        </p>
                    </div>
                    <div className="col-md-8">
                        <div className="float-end">
                            <Pagination links={entries.links} query={query} />
                        </div>
                    </div>
                </div>
            )}
        </>
    );
};

export default Index;
