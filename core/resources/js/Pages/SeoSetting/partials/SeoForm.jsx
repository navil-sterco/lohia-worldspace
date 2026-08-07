import TextInput from '@/Components/Form/TextInput';
import Type from 'lucide-react/dist/esm/icons/type.js';

const SeoForm = ({ data, errors, processing, onDataChange, children, keywordInput,setData, fileInputRef,handleKeyPress, setKeywordInput, addKeyword, searchTermsInput, setSearchTermsInput, addSearchTerm, removeSearchTerm, handleSearchTermKeyPress, progress,removeKeyword,seo,appUrl }) => {
    return (
        <>
            <div className="row">
                <div className="mb-3 col-md-12">
                    <TextInput
                        name="meta_title"
                        label="Meta Title"
                        value={data.meta_title}
                        onChange={(value) => onDataChange("meta_title", value)}
                        error={errors.meta_title}
                        placeholder="Page Title for SEO (50-60 characters)"
                        required={true}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                    <div className="form-text text-muted">
                        {data.meta_title.length}/60 characters recommended
                    </div>
                </div>
                
                <div className="mb-3 col-12">
                    <label htmlFor="meta_description" className="form-label">Meta Description</label>
                    <textarea
                        className="form-control"
                        id="meta_description"
                        name="meta_description"
                        rows="3"
                        placeholder='Page description for search engines (150-160 characters)'
                        value={data.meta_description}
                        onChange={(e) => setData("meta_description", e.target.value)}
                        maxLength={160}
                    />
                    <div className="form-text text-danger">{errors.meta_description}</div>
                    <div className="form-text text-muted">
                        {data.meta_description.length}/160 characters recommended
                    </div>
                </div>

                {/* Keywords */}
                <div className="mb-3 col-12">
                    <label className="form-label">Keywords</label>
                    <div className="input-group">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Add keyword and press Enter or click Add"
                            value={keywordInput}
                            onChange={(e) => setKeywordInput(e.target.value)}
                            onKeyPress={handleKeyPress}
                        />
                        <button
                            type="button"
                            className="btn btn-outline-primary"
                            onClick={addKeyword}
                            disabled={!keywordInput.trim()}
                        >
                            Add
                        </button>
                    </div>
                    <div className="form-text text-danger">{errors.keywords}</div>
                    
                    {/* Display selected keywords */}
                    {data.keywords.length > 0 && (
                        <div className="mt-2">
                            <div className="d-flex flex-wrap gap-2">
                                {data.keywords.map((keyword, index) => (
                                    <span key={index} className="badge bg-primary d-flex align-items-center">
                                        {keyword}
                                        <button
                                            type="button"
                                            className="btn-close btn-close-white ms-2"
                                            style={{ fontSize: '0.7rem' }}
                                            onClick={() => removeKeyword(index)}
                                            aria-label={`Remove ${keyword}`}
                                        />
                                    </span>
                                ))}
                            </div>
                            <div className="form-text text-muted mt-1">
                                {data.keywords.length} keyword(s) added
                            </div>
                        </div>
                    )}
                </div>

                {/* Search Terms */}
                <div className="mb-3 col-12">
                    <label className="form-label">Search Terms (Site Search)</label>
                    <div className="input-group">
                        <input
                            type="text"
                            className="form-control"
                            placeholder="Add search term and press Enter or click Add"
                            value={searchTermsInput}
                            onChange={(e) => setSearchTermsInput(e.target.value)}
                            onKeyPress={handleSearchTermKeyPress}
                        />
                        <button
                            type="button"
                            className="btn btn-outline-primary"
                            onClick={addSearchTerm}
                            disabled={!searchTermsInput.trim()}
                        >
                            Add
                        </button>
                    </div>
                    <div className="form-text text-danger">{errors.search_terms}</div>
                    
                    {/* Display selected search terms */}
                    {data.search_terms.length > 0 && (
                        <div className="mt-2">
                            <div className="d-flex flex-wrap gap-2">
                                {data.search_terms.map((term, index) => (
                                    <span key={index} className="badge bg-secondary d-flex align-items-center">
                                        {term}
                                        <button
                                            type="button"
                                            className="btn-close btn-close-white ms-2"
                                            style={{ fontSize: '0.7rem' }}
                                            onClick={() => removeSearchTerm(index)}
                                            aria-label={`Remove ${term}`}
                                        />
                                    </span>
                                ))}
                            </div>
                            <div className="form-text text-muted mt-1">
                                {data.search_terms.length} search term(s) added
                            </div>
                        </div>
                    )}
                </div>

                {/* Url */}
                <div className="mb-3 col-md-6">
                    <TextInput
                        name="url"
                        label="Url"
                        value={data.url}
                        onChange={(value) => onDataChange("url", value)}
                        error={errors.url}
                        placeholder="url-friendly-version"
                        required={true}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                </div>

                {/* Canonical URL */}
                <div className="mb-3 col-md-6">
                    <TextInput
                        name="canonical_url"
                        label="Canonical URL"
                        value={data.canonical_url}
                        onChange={(value) => onDataChange("canonical_url", value)}
                        error={errors.canonical_url}
                        placeholder="https://example.com/canonical-page"
                        required={false}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                </div>

                {/* OG Title */}
                <div className="mb-3 col-md-6">
                    <TextInput
                        name="og_title"
                        label="Open Graph Title"
                        value={data.og_title}
                        onChange={(value) => onDataChange("og_title", value)}
                        error={errors.og_title}
                        placeholder="Title for social media sharing"
                        required={false}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                </div>

                {/* OG Type */}
                <div className="mb-3 col-md-6">
                    <label htmlFor="og_type" className="form-label">Open Graph Type</label>
                    <select
                        id="og_type"
                        className="form-select"
                        value={data.og_type}
                        onChange={(e) => setData("og_type", e.target.value)}
                    >
                        <option value="website">Website</option>
                        <option value="article">Article</option>
                        <option value="product">Product</option>
                        <option value="profile">Profile</option>
                        <option value="video">Video</option>
                    </select>
                    <div className="form-text text-danger">{errors.og_type}</div>
                </div>

                {/* OG Description */}
                <div className="mb-3 col-12">
                    <label htmlFor="og_description" className="form-label">Open Graph Description</label>
                    <textarea
                        className="form-control"
                        id="og_description"
                        name="og_description"
                        rows="3"
                        placeholder='Description for social media sharing'
                        value={data.og_description}
                        onChange={(e) => setData("og_description", e.target.value)}
                        maxLength={160}
                    />
                    <div className="form-text text-danger">{errors.og_description}</div>
                </div>

                {/* OG URL */}
                <div className="mb-3 col-md-6">
                    <TextInput
                        name="og_url"
                        label="Open Graph URL"
                        value={data.og_url}
                        onChange={(value) => onDataChange("og_url", value)}
                        error={errors.og_url}
                        placeholder="URL for social media sharing"
                        required={false}
                        disabled={processing}
                        icon={<Type size={16} />}
                    />
                </div>

                {/* OG Image */}
                <div className="mb-3 col-md-6">
                    <label className="form-label" htmlFor="og_image">Open Graph Image</label>
                    <input
                        type="file"
                        id="og_image"
                        className="form-control"
                        ref={fileInputRef}
                        onChange={(e) => setData("og_image", e.target.files[0])}
                        accept="image/png, image/jpeg, image/webp"
                    />
                    <div className="form-text text-danger">{errors.og_image}</div>
                    <div className="form-text">Recommended: 1200x630px for social media sharing</div>
                </div>

                {/* Current OG Image Preview */}
                {seo?.og_image && (
                    <div className="mb-3 col-md-6">
                        <label className="form-label">Current Open Graph Image</label>
                        <div className="mb-2">
                            <img
                                src={`${appUrl}/${seo.og_image}`}
                                alt="Current OG Image"
                                style={{
                                    width: "150px",
                                    height: "80px",
                                    objectFit: "cover",
                                    borderRadius: "4px"
                                }}
                            />
                        </div>
                    </div>
                )}
            </div>

            {/* Progress Bar */}
            {progress && (
                <div className="progress mt-2">
                    <div
                        className="progress-bar"
                        role="progressbar"
                        style={{ width: `${progress.percentage}%` }}
                        aria-valuenow={progress.percentage}
                        aria-valuemin="0"
                        aria-valuemax="100"
                    >
                        {progress.percentage}%
                    </div>
                </div>
            )}

            {children}
        </>
    );
};

export default SeoForm;