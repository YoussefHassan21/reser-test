// resources/js/components/SectionList.jsx
import React, { useState } from 'react';

const Section = ({ section }) => {
    const [isExpanded, setIsExpanded] = useState(false);

    const toggleExpand = () => {
        setIsExpanded(!isExpanded);
    };

    return (
        <div style={{ backgroundColor: '#e0e0e0', color: '#333', margin: '10px 0', padding: '10px' }}>
            <div onClick={toggleExpand} style={{ cursor: 'pointer', display: 'flex', alignItems: 'center' }}>
                <i className={`fas fa-chevron-${isExpanded ? 'down' : 'right'} mr-2`}></i>
                <span>{section.name}</span>
            </div>
            {isExpanded && (
                <div style={{ marginLeft: '20px', paddingTop: '10px' }}>
                    {/* Affichage des champs */}
                    {section.fields && section.fields.length > 0 && (
                        <div>
                            <strong>Champs:</strong>
                            {section.fields.map((field) => (
                                <p key={field.id}>
                                    <a href="#">{field.name}</a>
                                </p>
                            ))}
                        </div>
                    )}
                    {/* Affichage des sous-sections */}
                    {section.subsections && section.subsections.length > 0 && (
                        <div>
                            <strong>Sous-sections:</strong>
                            {section.subsections.map((subsection) => (
                                <p key={subsection.id}>
                                    <a href={`/sections/${subsection.id}`}>{subsection.name}</a>
                                </p>
                            ))}
                        </div>
                    )}
                </div>
            )}
        </div>
    );
};

const SectionList = ({ sections }) => {
    return (
        <div>
            {sections.map((section) => (
                <Section key={section.id} section={section} />
            ))}
        </div>
    );
};

export default SectionList;
