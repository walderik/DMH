document.getElementById('autocomplete_person').addEventListener('input', function() {
	let query = this.value;
    if (query.length > 2) {
        fetch("../ajax/autocomplate_person.php", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ search: query })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
        	console.log('Data:', data);
            let suggestions = document.getElementById('suggestions');
            suggestions.innerHTML = '';
             if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            data.forEach(item => {
                let div = document.createElement('div');
                div.textContent = item[1];
                div.dataset.id = item[0]; // Store the id in a data attribute
                div.style.cursor = 'pointer';
                div.addEventListener('click', function() {
					// Update the HTML fields
					document.getElementById('autocomplete_person').value = item[1];
        			document.getElementById('person_id').value = item[0];
        			// Clear the suggestions
                    suggestions.innerHTML = '';
                });
                suggestions.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error:', error);
        });
    }
});