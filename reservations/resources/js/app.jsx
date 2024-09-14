import React from 'react';
import ReactDOM from 'react-dom';
import LoginForm from './components/LoginForm.jsx'; // Assurez-vous que l'extension est .jsx
import ActionButtons from './components/ActionButtons.jsx';
import SectionList from './components/SectionList';

// Fonction de gestion des clics pour "Ajouter une section"
const handleAddSection = () => {
    window.location.href = '/sections/create'; // Redirige vers la page de création de section
};

// Fonction de gestion des clics pour "Ajouter un champ"
const handleAddField = () => {
    window.location.href = '/fields/create'; // Redirige vers la page de création de champ
};

// Monter le composant LoginForm si l'élément avec l'ID 'login' existe
if (document.getElementById('login')) {
    ReactDOM.render(<LoginForm />, document.getElementById('login'));
}

// Monter le composant ActionButtons si l'élément avec l'ID 'action-buttons' existe
if (document.getElementById('action-buttons')) {
    ReactDOM.render(
        <ActionButtons 
            onAddSection={handleAddSection} 
            onAddField={handleAddField} 
        />, 
        document.getElementById('action-buttons')
    );
}
if (document.getElementById('section-list')) {
    ReactDOM.render(
        <SectionList sections={window.sectionsData} />, 
        document.getElementById('section-list')
    );
}
