function autocomplete_person(ev, person, number) {
	autocomplete_person_larp(ev, person, number, null);
}
	
function autocomplete_person_larp(ev, person, number, larpid) {
	let query = person.value;
    if (query.length > 2) {
        fetch("../ajax/autocomplate_person.php", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ search: query, larpid: larpid })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
        	console.log('Data from PHP:', data);
        	console.log('Query 2:', query);

            let suggestions = document.getElementById('suggestions'+number);
            suggestions.innerHTML = '';
            if (data.error) {
                console.error('Error:', data.error);
                return;
            }
            data.forEach(item => {
                let div = document.createElement('div');
                let matchIndex = item[1].toLowerCase().indexOf(query.toLowerCase());
                
                console.log('MatchIndex:', matchIndex);
                if (matchIndex !== -1) {
                    let beforeMatch = item[1].substr(0, matchIndex);
                    let matchText = item[1].substr(matchIndex, query.length);
                    let afterMatch = item[1].substr(matchIndex + query.length);
                    div.innerHTML = beforeMatch + "<strong>" + matchText + "</strong>" + afterMatch;
                } else {
                    div.textContent = item[1];
                }
                div.dataset.id = item[0]; // Store the id in a data attribute
                div.style.cursor = 'pointer';
                div.addEventListener('click', function() {
					// Update the HTML fields
					document.getElementById('autocomplete_person'+number).value = item[1];
        			document.getElementById('person_id'+number).value = item[0];
        			// Clear the suggestions
                    suggestions.innerHTML = '';
                });
                suggestions.appendChild(div);
            });
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error:'+ error);
        });
    }
}