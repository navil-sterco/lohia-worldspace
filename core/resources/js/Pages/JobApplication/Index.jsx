import React from "react";
import SubmissionIndex from "@/Components/SubmissionIndex";

const fields = [
    { key: "job_slug" },
    { key: "job_title" },
    { key: "name" },
    { key: "email" },
    { key: "phone" },
    { key: "best_time_to_call", label: "Best Time To Call" },
    {
        key: "cv_path",
        label: "CV File",
        file: true,
        nameKey: "cv_original_name",
    },
    { key: "cv_original_name", label: "Original CV Name" },
    { key: "created_at", label: "Submitted On" },
    { key: "updated_at", label: "Updated On" },
];

export default function Index(props) {
    return (
        <SubmissionIndex
            {...props}
            title="Job Applications"
            routePrefix="job-applications"
            searchPlaceholder="Search by applicant name, email, or job..."
            columns={[
                { key: "name", label: "Name" },
                { key: "email", label: "Email" },
                { key: "phone", label: "Phone" },
                { key: "job_title", label: "Job" },
            ]}
            fields={fields}
        />
    );
}
