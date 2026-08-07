import React from "react";
import { Editor } from "@tinymce/tinymce-react";
import { usePage } from "@inertiajs/react";


const WisyswigEditor = ({
    value,
    onChange,
    placeholder = "",
    height = "300px",
    className = "",
    ...props
}) => {
    const { appUrl } = usePage().props;
    return (
        <div
            className={`code-editor-wrapper ${className}`}
            style={{
                border: "1px solid #ddd",
                borderRadius: "4px",
                overflow: "hidden",
                backgroundColor: "#fff",
            }}
        >
            <Editor
                tinymceScriptSrc={`${appUrl}/editor/tinymce/tinymce.min.js`}
                value={value}
                onEditorChange={(content) => onChange(content || "")}
                init={{
                    height: parseInt(height),
                    menubar: true,

                    plugins: [
                        "advlist",
                        "autolink",
                        "lists",
                        "link",
                        "image",
                        "charmap",
                        "anchor",
                        "searchreplace",
                        "visualblocks",
                        "code",
                        "fullscreen",
                        "insertdatetime",
                        "table",
                        "preview",
                        "wordcount",
                    ],

                    toolbar:
                        "undo redo | formatselect | bold italic underline strikethrough | " +
                        "alignleft aligncenter alignright alignjustify | " +
                        "bullist numlist outdent indent | " +
                        "table | code fullscreen",

                    relative_urls: false,
                    remove_script_host: false,
                    convert_urls: false,

                    extended_valid_elements: "span[*]",
                    valid_children: "+body[style]",
                    verify_html: false,
                    cleanup: false,

                    content_style:
                        "body { font-family:Helvetica,Arial,sans-serif; font-size:14px; }",

                    placeholder: placeholder,
                }}
                {...props}
            />
        </div>
    );
};

export default WisyswigEditor;