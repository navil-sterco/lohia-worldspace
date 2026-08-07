import { usePage } from '@inertiajs/react';
import { Link as InertiaLink } from '@inertiajs/react';
import React, { useEffect, useState } from 'react';
import {
    FileText, Grid, Users, Image as ImageIcon, Activity, Flag, Globe,
    Plus, Upload, Check, Edit2, Clock, Inbox, Eye, TrendingUp,
    Mail, Phone, MessageSquare, Calendar, ChevronRight
} from 'lucide-react';

const Badge = ({ variant = 'success', children }) => {
    const map = {
        success: 'bg-label-success text-success',
        warning: 'bg-label-warning text-warning',
        info: 'bg-label-info text-info',
        danger: 'bg-label-danger text-danger',
        secondary: 'bg-label-secondary text-secondary',
    };
    return (
        <span className={`badge ${map[variant]}`} style={{ fontSize: 11 }}>
            {children}
        </span>
    );
};

const AvatarIcon = ({ bgClass, icon: IconComponent, size = 36 }) => (
    <div className={`avatar flex-shrink-0`} style={{ width: size, height: size }}>
        <span className={`avatar-initial rounded ${bgClass}`} style={{ width: size, height: size, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
            <IconComponent size={16} />
        </span>
    </div>
);

const ProgressBar = ({ value, color = 'bg-primary', height = 6 }) => (
    <div className="progress" style={{ height }}>
        <div className={`progress-bar ${color}`} style={{ width: `${Math.min(100, Math.max(0, value))}%` }} />
    </div>
);

// ── Stat cards row (top 4) ───────────────────────────────────────────────────

const MiniStatCard = ({ label, value, icon, bgClass, delta, deltaVariant }) => (
    <div className="col-lg-3 col-md-6 col-6 mb-4">
        <div className="card">
            <div className="card-body">
                <div className="d-flex align-items-start justify-content-between mb-2">
                    <AvatarIcon bgClass={bgClass} icon={icon} />
                    <Badge variant={deltaVariant}>{delta}</Badge>
                </div>
                <p className="mb-1 text-muted" style={{ fontSize: 12 }}>{label}</p>
                <h4 className="mb-0 fw-semibold">{value}</h4>
            </div>
        </div>
    </div>
);

// ── Progress stat card (Pages / Sections) ────────────────────────────────────

const ProgressStatCard = ({ label, total, totalLabel, percent, barColor, icon, bgClass }) => (
    <div className="col-lg-6 col-md-6 col-6 mb-4">
        <div className="card h-100">
            <div className="card-body">
                <div className="card-title d-flex align-items-start justify-content-between mb-2">
                    <AvatarIcon bgClass={bgClass} icon={icon} />
                </div>
                <span className="d-block mb-1 text-muted" style={{ fontSize: 12 }}>{label}</span>
                <h4 className="mb-0 fw-semibold">{total}</h4>
                <div className="mt-3">
                    <div className="d-flex justify-content-between mb-1">
                        <small className="text-muted">{totalLabel}</small>
                        <small className="fw-semibold">{percent}%</small>
                    </div>
                    <ProgressBar value={percent} color={barColor} />
                </div>
            </div>
        </div>
    </div>
);

// ── Quick action button ───────────────────────────────────────────────────────

const QuickAction = ({ label, sub, icon, bgClass, href }) => (
    <InertiaLink href={href || '#'} className="d-flex align-items-center gap-3 p-2 rounded mb-2 text-decoration-none"
        style={{ background: 'var(--bs-body-bg, #f8f7fa)', transition: 'background .15s' }}
        onMouseEnter={e => e.currentTarget.style.background = 'rgba(105,108,255,.08)'}
        onMouseLeave={e => e.currentTarget.style.background = 'var(--bs-body-bg, #f8f7fa)'}>
        <AvatarIcon bgClass={bgClass} icon={icon} size={32} />
        <div>
            <p className="mb-0 fw-semibold" style={{ fontSize: 13 }}>{label}</p>
            <small className="text-muted">{sub}</small>
        </div>
    </InertiaLink>
);

// ── Publish rate bars ─────────────────────────────────────────────────────────

const RateBar = ({ label, count, total, color }) => {
    const pct = total > 0 ? Math.round((count / total) * 100) : 0;
    return (
        <div className="mb-3">
            <div className="d-flex justify-content-between mb-1">
                <small className="text-muted">{label}</small>
                <small className="fw-semibold">{count} <span className="text-muted">({pct}%)</span></small>
            </div>
            <ProgressBar value={pct} color={color} />
        </div>
    );
};

const PageListItem = ({ page }) => (
    <li className="d-flex mb-3 pb-3 border-bottom align-items-center gap-3">
        <div className="avatar flex-shrink-0">
            <span className={`avatar-initial rounded ${page.is_published ? 'bg-label-success' : 'bg-label-warning'}`}
                style={{ width: 36, height: 36, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                {page.is_published ? <Check size={15} /> : <Clock size={15} />}
            </span>
        </div>
        <div className="d-flex w-100 flex-column gap-1">
            <div className="d-flex align-items-center justify-content-between">
                <div>
                    <h6 className="mb-0" style={{ fontSize: 13 }}>{page.title}</h6>
                    <small className="text-muted">/{page.slug}</small>
                </div>
                <div className="d-flex align-items-center gap-2">
                    <small className="text-muted">{page.updated_at}</small>
                    <InertiaLink href={route('pages.edit', page.id)} className="btn btn-sm btn-icon btn-text-primary p-0">
                        <Edit2 size={15} />
                    </InertiaLink>
                </div>
            </div>
        </div>
    </li>
);


const EnquiryItem = ({ enquiry }) => (
    <div className="d-flex mb-3 pb-3 border-bottom align-items-start gap-3">
        <div className="avatar flex-shrink-0">
            <span className="avatar-initial rounded bg-label-info"
                style={{ width: 36, height: 36, display: 'flex', alignItems: 'center', justifyContent: 'center' }}>
                <Mail size={15} />
            </span>
        </div>
        <div className="d-flex w-100 flex-column gap-1 flex-grow-1">
            <div className="d-flex align-items-start justify-content-between">
                <div className="flex-grow-1">
                    <h6 className="mb-1" style={{ fontSize: 13 }}>{enquiry.name}</h6>
                    <small className="text-muted d-block">{enquiry.email}</small>
                </div>
                <small className="text-muted ms-2">{enquiry.created_at}</small>
            </div>
            {enquiry.description && (
                <small className="text-muted mt-2 d-block" style={{ fontSize: 11 }}>{enquiry.description}</small>
            )}
        </div>
    </div>
);


const Dashboard = ({ stats = {}, recentPages = [], recentSections = [], recentEnquiries = [] }) => {
    const { appUrl } = usePage().props;
    const { webUrl } = usePage().props;
    const [editMode, setEditMode] = useState(false);

    useEffect(() => {
        if (typeof dashboardAnalitics === 'function') dashboardAnalitics();
    }, []);

    const totalPages = stats.total_pages ?? 0;
    const publishedPages = stats.published_pages ?? 0;
    const draftPages = stats.draft_pages ?? 0;
    const archivedPages = stats.archived_pages ?? 0;
    const publishedPct = stats.tottal_pages_published_percent ?? 0;

    const totalSections = stats.total_pages_section ?? 0;
    const activeSectionPct = stats.tottal_pages_section_active_percent ?? 0;

    const totalAdmins = stats.total_users ?? 0;
    const totalMenus = stats.total_menus ?? 0;
    const totalMedia = stats.total_images ?? 0;
    const totalEnquiries = stats.total_enquiries ?? 0;
    const pageViews = totalPages > 0 ? Math.round(totalPages * 1.75) : 0;

    // Sample pages if none provided
    const pages = recentPages.length > 0 ? recentPages : [];

    const sections = recentSections.length > 0 ? recentSections : [];

    const sectionStatusBadge = (status) => {
        if (status === 'active') return <Badge variant="success">Active</Badge>;
        if (status === 'inactive') return <Badge variant="warning">Inactive</Badge>;
        return <Badge variant="secondary">Draft</Badge>;
    };

    return (
        <>
            <div className="row">
                <div className="col-lg-8 mb-4 order-0">
                    <div className="card">
                        <div className="d-flex align-items-end row">
                            <div className="col-sm-7">
                                <div className="card-body">
                                    <h5 className="card-title text-primary">
                                        Welcome to Lohia Worldspace CMS 🎉
                                    </h5>
                                    <p className="mb-4">
                                        Manage your university's digital presence efficiently with this comprehensive content management system.
                                    </p>
                                    <a target='_blank' aria-label="view website" href={webUrl} className="btn btn-sm btn-outline-primary">
                                        View Website
                                    </a>
                                </div>
                            </div>
                            <div className="col-sm-5 text-center text-sm-left">
                                <div className="card-body pb-0 px-0 px-md-4">
                                    <img
                                        src={`${appUrl}/assets/img/illustrations/man-with-laptop-light.png`}
                                        height="140"
                                        alt="Welcome illustration"
                                        data-app-dark-img="illustrations/man-with-laptop-dark.png"
                                        data-app-light-img="illustrations/man-with-laptop-light.png"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div className="col-lg-4 col-md-4 order-1">
                    <div className="row">
                        <ProgressStatCard
                            label="Pages"
                            total={totalPages}
                            totalLabel="Total Pages"
                            percent={publishedPct}
                            barColor="bg-success"
                            icon={FileText}
                            bgClass="bg-label-success"
                        />
                        <ProgressStatCard
                            label="Sections"
                            total={totalSections}
                            totalLabel="Total Sections"
                            percent={activeSectionPct}
                            barColor="bg-info"
                            icon={Grid}
                            bgClass="bg-label-info"
                        />
                    </div>
                </div>
            </div>

            <div className="row">
                <MiniStatCard label="Admins" value={totalAdmins} icon={Users} bgClass="bg-label-warning" delta={totalAdmins > 0 ? '+' + totalAdmins : '0'} deltaVariant="warning" />
                <MiniStatCard label="Menus" value={totalMenus} icon={Flag} bgClass="bg-label-danger" delta={totalMenus > 0 ? '+' + totalMenus : '0'} deltaVariant="danger" />
                <MiniStatCard label="Media Files" value={totalMedia} icon={ImageIcon} bgClass="bg-label-primary" delta={totalMedia > 0 ? '+' + totalMedia : '0'} deltaVariant="info" />
                <MiniStatCard label="Enquiries" value={totalEnquiries} icon={MessageSquare} bgClass="bg-label-success" delta={totalEnquiries > 0 ? '+' + totalEnquiries : '0'} deltaVariant="success" />
            </div>

            <div className="row">
                <div className="col-md-6 col-lg-6 order-2 mb-4">
                    <div className="card h-100">
                        <div className="card-header d-flex align-items-center justify-content-between pb-0">
                            <div className="card-title mb-0">
                                <h5 className="m-0 me-2">Recent Pages</h5>
                                <small className="text-muted">{pages.length} pages</small>
                            </div>
                            <InertiaLink href={route('pages.index')} className="btn btn-sm btn-outline-primary">
                                View All
                            </InertiaLink>
                        </div>
                        <div className="card-body pt-3">
                            {pages.length > 0 ? (
                                <ul className="p-0 m-0" style={{ listStyle: 'none' }}>
                                    {pages.map(page => <PageListItem key={page.id} page={page} />)}
                                </ul>
                            ) : (
                                <div className="text-center py-4">
                                    <Inbox size={32} color="#ccc" />
                                    <p className="text-muted mt-2 mb-0">No pages created yet</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>

                <div className="col-md-6 col-lg-6 order-2 mb-4">
                    <div className="card h-100">
                        <div className="card-header d-flex align-items-center justify-content-between pb-0">
                            <div className="card-title mb-0">
                                <h5 className="m-0 me-2">Recent Sections</h5>
                                <small className="text-muted">{sections.length} sections</small>
                            </div>
                            <button className="btn btn-sm btn-outline-primary">View All</button>
                        </div>
                        <div className="card-body pt-3">
                            <table className="table table-hover" style={{ fontSize: 13 }}>
                                <thead>
                                    <tr>
                                        <th className="text-muted fw-normal" style={{ fontSize: 11 }}>SECTION</th>
                                        <th className="text-muted fw-normal" style={{ fontSize: 11 }}>PAGE</th>
                                        <th className="text-muted fw-normal" style={{ fontSize: 11 }}>STATUS</th>
                                        <th className="text-muted fw-normal" style={{ fontSize: 11 }}>ACTION</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {sections?.map(s => (
                                        <tr key={s.id}>
                                            <td className="fw-semibold">{s.title}</td>
                                            <td className="text-muted">{s.page}</td>
                                            <td>{sectionStatusBadge(s.status)}</td>
                                            <td>
                                                <InertiaLink href={route('page-sections.edit', s.id)} className="btn btn-sm btn-icon btn-text-primary p-0" title="Edit section">
                                                    <Edit2 size={14} />
                                                </InertiaLink>
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div className="row">
                <div className="col-md-4 mb-4">
                    <div className="card h-100">
                        <div className="card-header d-flex align-items-center justify-content-between pb-0">
                            <h5 className="card-title m-0">Quick Actions</h5>
                            {editMode && <button className="btn btn-sm btn-link p-0" onClick={() => setEditMode(false)}>Done</button>}
                        </div>
                        <div className="card-body pt-3">
                            <QuickAction label="New Page" sub="Create a blank page" icon={Plus} bgClass="bg-label-primary" href={route('pages.create')} />
                            <QuickAction label="Add Section" sub="Insert a new section block" icon={Grid} bgClass="bg-label-success" href={route('page-sections.create')} />
                            <QuickAction label="Upload Media" sub="Add images or files" icon={Upload} bgClass="bg-label-info" href={route('gallery.create')} />
                        </div>
                    </div>
                </div>

                <div className="col-md-4 mb-4">
                    <div className="card h-100">
                        <div className="card-header d-flex align-items-center justify-content-between pb-0">
                            <h5 className="card-title m-0">Publish Rate</h5>
                            <span className="badge bg-label-primary">{publishedPct}%</span>
                        </div>
                        <div className="card-body pt-3">
                            <p className="text-muted mb-3" style={{ fontSize: 13 }}>
                                {publishedPages} of {totalPages} pages published
                            </p>
                            <RateBar label="Published" count={publishedPages} total={totalPages} color="bg-success" />
                            <RateBar label="Draft" count={draftPages} total={totalPages} color="bg-warning" />
                        </div>
                    </div>
                </div>

                <div className="col-md-4 mb-4">
                    <div className="card h-100">
                        <div className="card-header d-flex align-items-center justify-content-between pb-0">
                            <div>
                                <h5 className="card-title m-0">Recent Enquiries</h5>
                                <small className="text-muted">{recentEnquiries.length} enquiries</small>
                            </div>
                            <InertiaLink href={route('contact-forms.index')} className="btn btn-sm btn-outline-secondary">
                                View All
                            </InertiaLink>
                        </div>
                        <div className="card-body pt-3">
                            {recentEnquiries.length > 0 ? (
                                recentEnquiries.map((enquiry) => (
                                    <EnquiryItem key={enquiry.id} enquiry={enquiry} />
                                ))
                            ) : (
                                <div className="text-center py-4">
                                    <MessageSquare size={32} color="#ccc" />
                                    <p className="text-muted mt-2 mb-0">No enquiries yet</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </>
    );
};

export default Dashboard;