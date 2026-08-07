import React from "react";
import { Link, useForm } from "@inertiajs/react";
import useCtrlSSubmit from "@/hooks/useCtrlSSubmit";

const Edit = ({ page, tabs, parentPages }) => {
    const safeJsonParse = (str) => {
        if (!str) return {};
        try {
            return JSON.parse(str);
        } catch (e) {
            console.error("JSON parse error:", e);
            return {};
        }
    };

    const normalizeDisplayLocation = (value) => {
        if (Array.isArray(value)) return value;
        if (!value) return [];
        if (typeof value === "string") {
            try {
                const parsed = JSON.parse(value);
                return Array.isArray(parsed) ? parsed : [value];
            } catch (e) {
                return [value];
            }
        }
        return [];
    };

    const { data, setData, put, errors, processing } = useForm({
        title: page.title || "",
        slug: page.slug || "",
        meta_description: page.meta_description || "",
        page_type: page.page_type || "modular",
        display_location: normalizeDisplayLocation(page.display_location),
        is_published: page.is_published || true,
        target_blank: page.target_blank || false,
        tab_id: page.tab_id || "",
        display_order: page.display_order || 1000,
        parent_page_id: page.parent_page_id || "",
        sections: page.sections
            ? page.sections.map((section) => ({
                  section_id: section.id,
                  order: section.pivot.order,
                  data: safeJsonParse(section.pivot.section_data),
              }))
            : [],
    });

    const hasParentPage = Boolean(data.parent_page_id);

    const displayLocationOptions = [
        { value: "header", label: "Header" },
        { value: "footer", label: "Footer" },
        { value: "quick-links", label: "Quick Links" },
        { value: "sidebar", label: "Sidebar" },
    ];

    const toggleDisplayLocation = (value) => {
        setData((prev) => {
            const exists = prev.display_location.includes(value);
            return {
                ...prev,
                display_location: exists
                    ? prev.display_location.filter((v) => v !== value)
                    : [...prev.display_location, value],
            };
        });
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        put(route("pages.update", page.id));
    };

    useCtrlSSubmit(handleSubmit, !processing);

    return (
        <>
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h1 className="text-muted mb-1">Edit Page</h1>
                    <p className="text-muted mb-0">
                        Update page content and sections
                    </p>
                </div>
            </div>

            <div className="card">
                <form onSubmit={handleSubmit}>
                    <div className="card-body">
                        <div className="row">
                            <div className="mb-3 col-md-6">
                                <label className="form-label">
                                    Page Title{" "}
                                    <span className="text-danger">*</span>
                                </label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={data.title}
                                    onChange={(e) =>
                                        setData("title", e.target.value)
                                    }
                                    placeholder="About Us"
                                />
                                {errors.title && (
                                    <div className="text-danger small">
                                        {errors.title}
                                    </div>
                                )}
                                <div className="form-text">
                                    The main title of your page
                                </div>
                            </div>

                            <div className="mb-3 col-md-6">
                                <label className="form-label">
                                    URL Slug
                                </label>
                                <input
                                    type="text"
                                    className="form-control"
                                    value={data.slug}
                                    onChange={(e) =>
                                        setData("slug", e.target.value)
                                    }
                                    placeholder="about-us"
                                />
                                {errors.slug && (
                                    <div className="text-danger small">
                                        {errors.slug}
                                    </div>
                                )}
                                <div className="form-text">
                                    If the slug is empty it will be automatically generated by title
                                </div>
                            </div>

                            <div className="mb-3 col-md-6">
                                <label className="form-label">
                                    Page Type{" "}
                                    <span className="text-danger">*</span>
                                </label>
                                <select
                                    className="form-select"
                                    value={data.page_type}
                                    onChange={(e) => {
                                        setData("page_type", e.target.value);
                                    }}
                                >
                                    <option value="modular">
                                        Modular Content Pages
                                    </option>
                                    <option value="cms">
                                        CMS Section Pages
                                    </option>
                                </select>
                                {errors.page_type && (
                                    <div className="text-danger small">
                                        {errors.page_type}
                                    </div>
                                )}
                                <div className="form-text">
                                    {data.page_type === "cms"
                                        ? "Traditional pages with predefined sections"
                                        : "Flexible pages with modular content blocks and sections"}
                                </div>
                            </div>

                            <div className="mb-3 col-md-6">
                                <label className="form-label">
                                    Parent Page
                                </label>
                                <select
                                    className="form-select"
                                    value={data.parent_page_id}
                                    onChange={(e) => {
                                        const selectedId = e.target.value;

                                        setData((prev) => ({
                                            ...prev,
                                            parent_page_id: selectedId,
                                            tab_id: selectedId
                                                ? ""
                                                : prev.tab_id,
                                        }));
                                    }}
                                >
                                    <option value="">No Parent Page</option>
                                    {parentPages.map((item) => (
                                        <option key={item.id} value={item.id}>
                                            {item.title} ({item.slug})
                                        </option>
                                    ))}
                                </select>
                                {errors.parent_page_id && (
                                    <div className="text-danger small">
                                        {errors.parent_page_id}
                                    </div>
                                )}
                                <div className="form-text">
                                    Optional. Child pages inherit the parent tab
                                    context and will not appear as a separate
                                    tab item.
                                </div>
                            </div>

                            <div className="mb-3 col-md-6">
                                <label className="form-label">
                                    Link to Tab
                                </label>
                                <select
                                    className="form-select"
                                    value={data.tab_id}
                                    onChange={(e) =>
                                        setData("tab_id", e.target.value)
                                    }
                                    disabled={hasParentPage}
                                >
                                    <option value="">No Tab (General)</option>
                                    {tabs.map((tab) => (
                                        <option key={tab.id} value={tab.id}>
                                            {tab.heading}
                                        </option>
                                    ))}
                                </select>
                                {errors.tab_id && (
                                    <div className="text-danger small">
                                        {errors.tab_id}
                                    </div>
                                )}
                                <div className="form-text">
                                    {hasParentPage
                                        ? "Disabled because this page uses the selected parent page tab context"
                                        : "Choose a tab to group this page under"}
                                </div>
                            </div>

                            <div className="mb-3 col-md-4">
                                <label className="form-label">
                                    Display Location
                                </label>
                                <div className="border d-flex flex-wrap gap-2 p-2 rounded">
                                    {displayLocationOptions.map((option) => (
                                        <div
                                            className="form-check"
                                            key={option.value}
                                        >
                                            <input
                                                type="checkbox"
                                                className="form-check-input"
                                                id={`display-location-${option.value}`}
                                                checked={data.display_location.includes(
                                                    option.value,
                                                )}
                                                onChange={() =>
                                                    toggleDisplayLocation(
                                                        option.value,
                                                    )
                                                }
                                            />
                                            <label
                                                className="form-check-label"
                                                htmlFor={`display-location-${option.value}`}
                                            >
                                                {option.label}
                                            </label>
                                        </div>
                                    ))}
                                </div>
                                {errors.display_location && (
                                    <div className="text-danger small">
                                        {errors.display_location}
                                    </div>
                                )}
                                <div className="form-text">
                                    Select one or more locations where this page should appear
                                </div>
                            </div>

                            <div className="mb-3 col-md-2">
                                <label className="form-label">
                                    Display Order
                                </label>
                                <input
                                    type="number"
                                    className="form-control"
                                    value={data.display_order}
                                    onChange={(e) =>
                                        setData("display_order", e.target.value)
                                    }
                                />
                                {errors.display_order && (
                                    <div className="text-danger small">
                                        {errors.display_order}
                                    </div>
                                )}
                            </div>

                            <div className="mb-3 col-4">
                                <div className="form-check form-switch">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={data.target_blank}
                                        onChange={(e) =>
                                            setData(
                                                "target_blank",
                                                e.target.checked,
                                            )
                                        }
                                        role="switch"
                                    />
                                    <label className="form-check-label">
                                        Target Blank
                                    </label>
                                </div>
                                <div className="form-text">
                                    If checked page will open in new tab
                                </div>
                            </div>

                            <div className="mb-3 col-4">
                                <div className="form-check form-switch">
                                    <input
                                        type="checkbox"
                                        className="form-check-input"
                                        checked={data.is_published}
                                        onChange={(e) =>
                                            setData(
                                                "is_published",
                                                e.target.checked,
                                            )
                                        }
                                        role="switch"
                                    />
                                    <label className="form-check-label">
                                        Publish Page
                                    </label>
                                </div>
                                <div className="form-text">
                                    Published pages are visible to visitors
                                </div>
                            </div>
                        </div>

                        <div className="mt-4 pt-3 border-top">
                            <button
                                type="submit"
                                className="btn btn-primary me-2"
                                disabled={processing}
                            >
                                {processing ? (
                                    <>
                                        <span className="spinner-border spinner-border-sm me-2" />
                                        Updating...
                                    </>
                                ) : (
                                    "Update Page"
                                )}
                            </button>
                            <Link
                                href={route("pages.index")}
                                className="btn btn-secondary"
                            >
                                Cancel
                            </Link>
                        </div>
                    </div>
                </form>
            </div>
        </>
    );
};

export default Edit;