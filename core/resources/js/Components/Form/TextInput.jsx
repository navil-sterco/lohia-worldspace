// Components/Form/TextInput.jsx (Enhanced)
import React from 'react';

const TextInput = ({
    name,
    label,
    value,
    onChange,
    error,
    placeholder = '',
    required = false,
    disabled = false,
    icon,
    type = 'text',
    helperText,
    className = '',
    labelClassName = '',
    inputClassName = '',
    wrapperClassName = '',
    ...props
}) => {
    const handleChange = (e) => {
        onChange(e.target.value);
    };
    
    return (
        <div className={`mb-2 ${wrapperClassName}`}>
            {label && (
                <label 
                    htmlFor={name} 
                    className={`form-label ${labelClassName}`}
                >
                    {label}

                    {helperText && !error && (
                        <span id={`${name}-help`} className="form-text" style={{marginLeft: '8px'}}>
                            {helperText}
                        </span>
                    )}
                    {required && <span className="text-danger ms-1">*</span>}
                </label>
            )}
            
            <div className="input-group">
                {icon && (
                    <span className="input-group-text">
                        {icon}
                    </span>
                )}
                
                <input
                    id={name}
                    name={name}
                    type={type}
                    value={value}
                    onChange={handleChange}
                    placeholder={placeholder}
                    disabled={disabled}
                    className={`
                        form-control
                        ${error ? 'is-invalid' : ''}
                        ${inputClassName}
                    `.trim()}
                    aria-describedby={
                        error ? `${name}-error` : 
                        helperText ? `${name}-help` : undefined
                    }
                    required={required}
                    {...props}
                />
            </div>
            
            
            {error && (
                <div id={`${name}-error`} className="invalid-feedback">
                    {error}
                </div>
            )}
        </div>
    );
};

export default TextInput;