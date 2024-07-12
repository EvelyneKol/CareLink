function sendEmail() {
    const recipient = 'email@example.com'; // Replace with the recipient's email address
    const subject = 'Subject of the email'; // Replace with your desired subject
    const body = 'Content of the email'; // Replace with your desired email content
    const mailtoLink = `mailto:${recipient}?subject=${subject}&body=${body}`;

    window.location.href = mailtoLink;
}

let rating = 0;

        function setRating(value) {
            rating = value;
            // You can add more styling or feedback based on the selected emoji if needed
        }

        function submitFeedback() {
            const suggestions = document.getElementById('suggestions').value;
            // Here you can use the 'rating' and 'suggestions' variables to handle the user's feedback
            console.log('Rating:', rating);
            console.log('Suggestions:', suggestions);
            // You might want to send this data to a server-side script for further processing/storage
            // For this example, it logs the data to the console
            alert('Thank you for your feedback!');
        }

// Initialize the map
var map = L.map('map').setView([38.247320, 21.736783], 16); // Set your preferred coordinates and zoom level

// Add the tile layer (replace with your desired map provider)
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    maxZoom: 18,
}).addTo(map);

// Add a marker for your headquarters
var headquartersMarker = L.marker([38.247320, 21.736783]).addTo(map); // Set your headquarters coordinates

// Add a popup to the marker
headquartersMarker.bindPopup("<b>Headquarters</b><br>Agiou Nikolaou 38").openPopup(); // Set your headquarters address