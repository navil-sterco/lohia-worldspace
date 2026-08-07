import React from "react";
import menuData from "../data/menuData.json";
import { Link, usePage } from "@inertiajs/react";
import NavLink from "@/Components/NavLink";

const itemMeetsPermission = (item, userPermissions) => {
    if (item.permissions?.length) {
        return item.permissions.some((p) => userPermissions.includes(p));
    }
    if (item.permission) {
        return userPermissions.includes(item.permission);
    }
    return true;
};

const filterMenuItem = (item, userPermissions) => {
    if (!item?.available) {
        return null;
    }

    const submenu = Array.isArray(item.submenu)
        ? item.submenu
              .map((sub) => filterMenuItem(sub, userPermissions))
              .filter(Boolean)
        : null;

    if (submenu) {
        if (submenu.length === 0 && !item.link) {
            return null;
        }

        if (!itemMeetsPermission(item, userPermissions)) {
            return null;
        }

        return { ...item, submenu };
    }

    if (!itemMeetsPermission(item, userPermissions)) {
        return null;
    }

    return item;
};

const Sidebar = () => {
    const { appLogo, modulesForSidebar = [], auth } = usePage().props;
    console.log(modulesForSidebar);
    const permissions = auth?.permissions ?? [];

    const enhancedMenuData = menuData
        .map((section) => {
            let filteredItems = section.items
                .map((item) => filterMenuItem(item, permissions))
                .filter(Boolean);

            if (
                section.header === "CMS & Elements" &&
                modulesForSidebar.length > 0
            ) {
                filteredItems = [
                    ...filteredItems,
                    {
                        text: "Elements",
                        icon: "bx bx-grid-alt",
                        available: true,
                        submenu: modulesForSidebar.map((m) => ({
                            text: m.name,
                            available: true,
                            link: "modules.entries.index",
                            params: [m.id],
                        })),
                    },
                ];
            }

            return { ...section, items: filteredItems };
        })
        .filter((section) => section.items.length > 0);

    return (
        <aside
            id="layout-menu"
            className="layout-menu menu-vertical menu bg-menu-theme bg-dark"
        >
            <div
                className="app-brand demo"
                style={{ zIndex: 10, justifyContent: "center" }}
            >
                <Link
                    href={route("dashboard")}
                    className="app-brand-link"
                    style={{
                        border: "1px solid #ccc",
                        padding: "10px 20px",
                        borderRadius: "8px",
                    }}
                >
                    <img src={appLogo} alt="logo" style={{ width: 200 }} />
                </Link>
            </div>

            <div className="menu-inner-shadow mb-1"></div>

            <ul className="menu-inner py-1 pb-4">
                {enhancedMenuData.map((section, idx) => (
                    <React.Fragment key={idx}>
                        {section.header && (
                            <li className="menu-header small text-uppercase">
                                <span className="menu-header-text">
                                    {section.header}
                                </span>
                            </li>
                        )}

                        {section.items.map((item, i) => (
                            <MenuItem key={i} item={item} />
                        ))}
                    </React.Fragment>
                ))}
            </ul>
        </aside>
    );
};

const MenuItem = ({ item }) => {
    const { url } = usePage();
    const hasSubmenu = Array.isArray(item.submenu) && item.submenu.length > 0;

    const isUrlMatch = (link, params = []) => {
        if (!link) return false;

        const linkBase = link.split(".")[0];

        if (link === "modules.entries.index" && params[0]) {
            return url.includes(`/modules/${params[0]}/entries`);
        }

        switch (linkBase) {
            case "pages":
                return url.includes("/pages");
            case "page-sections":
                return url.includes("/page-sections");
            case "modules":
                return url.includes("/modules") && !url.includes("/entries");
            case "images":
                return url.includes("/images");
            case "menu":
                return url.includes("/menu");
            case "profile":
                return url.includes("/profile");
            default:
                return route().current(link, params);
        }
    };

    const isActive = isUrlMatch(item.link, item.params);
    const isSubmenuActive = hasSubmenu
        ? item.submenu.some((sub) => isUrlMatch(sub.link, sub.params))
        : false;

    const href = item.link
        ? route(item.link, item.params ?? [])
        : (item.href ?? "#");

    return (
        <li
            className={`menu-item ${
                isActive || isSubmenuActive ? "active" : ""
            } ${hasSubmenu && isSubmenuActive ? "open" : ""}`}
        >
            <NavLink
                href={href}
                className={`menu-link ${hasSubmenu ? "menu-toggle" : ""}`}
            >
                {item.icon && (
                    <i className={`menu-icon tf-icons ${item.icon}`}></i>
                )}
                <div>{item.text}</div>
            </NavLink>

            {hasSubmenu && (
                <ul className="menu-sub">
                    {item.submenu.map((sub, idx) => (
                        <MenuItem key={idx} item={sub} />
                    ))}
                </ul>
            )}
        </li>
    );
};

export default Sidebar;
