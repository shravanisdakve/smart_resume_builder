document.addEventListener('DOMContentLoaded', function() {
    const suggestionsList = document.getElementById('suggestions-list');
    const suggestions = JSON.parse(localStorage.getItem('resumeSuggestions'));

    if (suggestions && suggestions.length > 0) {
        suggestions.forEach(suggestion => {
            const li = document.createElement('li');
            li.textContent = suggestion;
            suggestionsList.appendChild(li);
        });
    } else {
        const li = document.createElement('li');
        li.textContent = 'No suggestions at the moment. Your resume looks great!';
        suggestionsList.appendChild(li);
    }
});