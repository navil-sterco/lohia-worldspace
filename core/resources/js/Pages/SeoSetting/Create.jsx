import { useForm } from '@inertiajs/react';
import React, { useRef, useState } from 'react'
import SeoForm from './partials/SeoForm';
import FormLayout from '../../Components/FormLayout';
import FormActions from '@/Components/FormActions';

const SeoCreate = (props) => {
    const [keywordInput, setKeywordInput] = useState("");
    const [searchTermsInput, setSearchTermsInput] = useState("");
    const { data, setData, post, progress, errors, processing } = useForm({
        meta_title: "",
        meta_description: "",
        canonical_url: "",
        url: "",
        og_title: "",
        og_description: "",
        og_image: null,
        og_type: "website",
        og_url: "",
        keywords: [],
        search_terms: [],
    });
    
    const fileInputRef = useRef(null);
    
    const handleSubmit = (e) => {
        e.preventDefault(); 
        post(route("seo.store"));
    };

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
}

export default SeoCreate;
