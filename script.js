function clearFields() {
    document.getElementById('stockcode').value = '';
    document.getElementById('productclass').value ='';
    document.getElementById('warehouse').value = '';
    document.getElementById('quantity').value = '';
    document.getElementById('transaction_type').value = '';
    document.getElementById('brand').value ='';
}
document.getElementById('undo').addEventListener('click', clearFields);


// Define the setTime function in the <script> tag.
function setTime() {
    const now = new Date();
    const timeString = now.toLocaleTimeString();
    document.getElementById("currentTime").textContent = "Current time: " + timeString;
}

 // Ensure that the event is only bound once the content has loaded.
 document.addEventListener('DOMContentLoaded', () => {
    // Select the button element with JavaScript.
    const button = document.getElementById("timebutton");
    // Bind the setTime() function to the button's click event.
    button.addEventListener('click', setTime);
});