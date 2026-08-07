import { useForm, usePage } from "@inertiajs/react";
import React, { useRef, useState, useEffect } from "react";
import SeoForm from "./partials/SeoForm";
import FormActions from '@/Components/FormActions';
import FormLayout from "../../Components/FormLayout";

const SeoEdit = ({ seo }) => {
    const fileInputRef = useRef(null);
    const appUrl = usePage().props.appUrl;
    const [keywordInput, setKeywordInput] = useState("");
    const [searchTermsInput, setSearchTermsInput] = useState("");

    const parseKeywords = (keywords) => {
        if (!keywords) return [];
        if (Array.isArray(keywords)) return keywords;
        try {
            return JSON.parse(keywords);
        } catch {
            return keywords.split(',').map(k => k.trim()).filter(k => k);
        }
    };

    const { data, setData, post, progress, errors, processing } = useForm({
        _method: "PUT",
        meta_title: seo.meta_title || "",
        meta_description: seo.meta_description || "",
        canonical_url: seo.canonical_url || "",
        url: seo.url || "",
        og_title: seo.og_title || "",
        og_description: seo.og_description || "",
        og_image: null,
        og_type: seo.og_type || "website",
        og_url: seo.og_url || "",
        keywords: parseKeywords(seo.keywords),
        search_terms: parseKeywords(seo.search_terms),
    });

    const addKeyword = () => {
        if (keywordInput.trim() && !data.keywords.includes(keywordInput.trim())) {
            setData("keywords", [...data.keywords, keywordInput.trim()]);
            setKeywordInput("");
        }
    };

    const removeKeyword = (index) => {
        const updatedKeywords = data.keywords.filter((_, i) => i !== index);
        setData("keywords", updatedKeywords);
    };

    const handleKeyPress = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addKeyword();
        }
    };

    const addSearchTerm = () => {
        if (searchTermsInput.trim() && !data.search_terms.includes(searchTermsInput.trim())) {
            setData("search_terms", [...data.search_terms, searchTermsInput.trim()]);
            setSearchTermsInput("");
        }
    };

    const removeSearchTerm = (index) => {
        const updatedSearchTerms = data.search_terms.filter((_, i) => i !== index);
        setData("search_terms", updatedSearchTerms);
    };

    const handleSearchTermKeyPress = (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            addSearchTerm();
        }
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const formData = new FormData();
        Object.keys(data).forEach((key) => {
            if (data[key] !== null) {
                if ((key === 'keywords' || key === 'search_terms') && Array.isArray(data[key])) {
                    formData.append(key, JSON.stringify(data[key]));
                } else {
                    formData.append(key, data[key]);
                }
            }
        });

        post(route("seo.update", seo.id), {
            data: formData,
            forceFormData: true,
            preserveScroll: true,
        });
    };

    const handleDataChange = (field, value) => {
        setData(field, value);
    };

    return (
        <>
            <FormLayout
                title="Create Seo Meta"
                subtitle="Add a new seo meta to the website"
                onSubmit={handleSubmit}
                processing={processing}
            >
                <SeoForm
                    data={data}
                    errors={errors}
                    processing={processing}
                    onDataChange={handleDataChange}
                    keywordInput={keywordInput}
                    setKeywordInput={setKeywordInput}
                    addKeyword={addKeyword}
                    searchTermsInput={searchTermsInput}
                    setSearchTermsInput={setSearchTermsInput}
                    addSearchTerm={addSearchTerm}
                    removeSearchTerm={removeSearchTerm}
                    handleSearchTermKeyPress={handleSearchTermKeyPress}
                    handleKeyPress ={handleKeyPress}
                    removeKeyword={removeKeyword}
                    fileInputRef ={fileInputRef}
                    progress={progress}
                    setData={setData}
                    seo={seo}
                    appUrl={appUrl}
                >
                    <FormActions
                        processing={processing}
                        submitText="Create Seo Meta"
                        cancelText="Cancel"
                        submitButtonProps={{
                            variant: 'primary',
                        }}
                    />
                </SeoForm>
            </FormLayout>
        </>
    );
};

export default SeoEdit;
