// Get the current date
const today = new Date();

// Format the date into Day of the week, Day, Month (e.g., Thursday, 11th January)
const options = { weekday: 'long', day: 'numeric', month: 'long' };
const formattedDate = today.toLocaleDateString('en-UK', options);

// Creates a html <p> element but its not yet part of the HTML document visible on screen. 
const dateElement = document.createElement('p');

// Set text inside <p> element;
dateElement.textContent = '${formattedDate}';

// Add <p> element to the page
document.body.appendChild(dateElement);
