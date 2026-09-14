import React from "react";
import SubmissionIndex from "@/Components/SubmissionIndex";

const fields = [
    { key: "email" },
    { key: "created_at", label: "Subscribed On" },
    { key: "updated_at", label: "Updated On" },
];

export default function Index(props) {
    return (
        <SubmissionIndex
            {...props}
            title="Newsletter Subscribers"
            routePrefix="newsletters"
            searchPlaceholder="Search by email..."
            columns={[
                { key: "email", label: "Email" },
                { key: "created_at", label: "Subscribed On" },
            ]}
            fields={fields}
        />
    );
}
