import React from 'react';

// Composant de bouton réutilisable
const Button = ({ label, onClick, className }) => (
    <button onClick={onClick} className={`btn ${className} m-1`}>
        {label}
    </button>
);

const ActionButtons = ({ onAddSection, onAddField }) => {
    return (
        <div className="d-flex justify-content-start flex-wrap mt-3">
            <Button label="Modifier" onClick={() => alert('Modifier')} className="btn-primary" />
            <Button label="Supprimer" onClick={() => alert('Supprimer')} className="btn-danger" />
            <Button label="Ajout d'une section" onClick={onAddSection} className="btn-secondary" />
            <Button label="Ajout d'un champ" onClick={onAddField} className="btn-secondary" />
            <Button label="Dupliquer" onClick={() => alert('Dupliquer')} className="btn-secondary" />
        </div>
    );
};

export default ActionButtons;
