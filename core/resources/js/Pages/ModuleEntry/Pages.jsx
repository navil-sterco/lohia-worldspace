import React from "react";
import { ToastContainer } from "react-toastify";
import "react-toastify/dist/ReactToastify.css";
import { useFlashMessage } from "@/hooks/useFlashMessage";
import { EntryPagesPanel } from "./EntryPagesPanel";

const Pages = ({
    module,
    entry,
    attachedPages = [],
    tabs = [],
    hasModuleSections = true,
}) => {
    useFlashMessage();

    return (
        <>
            <ToastContainer />
            <EntryPagesPanel
                module={module}
                entry={entry}
                entryLabel={entry?.slug || `Entry #${entry?.id}`}
                attachedPages={attachedPages}
                tabs={tabs}
                hasModuleSections={hasModuleSections}
                embedded={false}
            />
        </>
    );
};

export default Pages;
