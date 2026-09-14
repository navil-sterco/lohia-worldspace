import React from "react";
import SubmissionIndex from "@/Components/SubmissionIndex";

const fields = [
    { key: "name" },
    { key: "pan" },
    { key: "country_code", label: "Country Code" },
    { key: "phone" },
    { key: "email" },
    { key: "address", wide: true },
    { key: "work_profile", wide: true },
    { key: "other_organizations", wide: true },
    { key: "region_of_operations", label: "Region Of Operations" },
    { key: "sales_team_member_name", label: "Sales Team Member" },
    { key: "company_name", label: "Company Name" },
    { key: "date_of_establishment", label: "Date Of Establishment" },
    { key: "organization_type", label: "Organization Type" },
    { key: "association_member", label: "Association Member" },
    { key: "business_type", label: "Business Type" },
    {
        key: "registration_certificate_path",
        label: "Registration Certificate",
        file: true,
    },
    { key: "rera_certificate_path", label: "RERA Certificate", file: true },
    { key: "registered_address", label: "Registered Address" },
    { key: "company_pan_details", label: "Company PAN Details" },
    { key: "gst_certificate_path", label: "GST Certificate", file: true },
    { key: "ip_address", label: "IP Address" },
    { key: "created_at", label: "Submitted On" },
    { key: "updated_at", label: "Updated On" },
];

export default function Index(props) {
    return (
        <SubmissionIndex
            {...props}
            title="Channel Partner Applications"
            routePrefix="channel-partners"
            searchPlaceholder="Search by name or email..."
            columns={[
                { key: "name", label: "Name" },
                { key: "company_name", label: "Company" },
                { key: "email", label: "Email" },
                { key: "phone", label: "Phone" },
            ]}
            fields={fields}
        />
    );
}
