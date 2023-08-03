// Get the file input element and submit button element
const fileInput = document.getElementById("formFile");
const submitButton = document.getElementById("submit_upload");

// Add an event listener to the file input
fileInput.addEventListener("change", function() {
    // Check if a file is selected
    if (fileInput.files.length > 0) {
        // Enable the submit button
        submitButton.disabled = false;
    } else {
        // If no file is selected, disable the submit button
        submitButton.disabled = true;
    }
});


function loadingLoader() {
    $('#uploadCashback').hide();

     // Show the loading screen
     $('#loadingScreen').fadeIn();
}

// Get the file input element and submit button element
const fileInputDelivery = document.getElementById("formFileDelivery");
const submitButtonDelivery = document.getElementById("submit_upload_delivery");

// Add an event listener to the file input
fileInputDelivery.addEventListener("change", function() {
    console.log(fileInputDelivery.files.length);
    // Check if a file is selected
    if (fileInputDelivery.files.length > 0) {
        // Enable the submit button
        submitButtonDelivery.disabled = false;
    } else {
        // If no file is selected, disable the submit button
        submitButtonDelivery.disabled = true;
    }
});


function loadingLoaderDelivery() {
    $('#uploadTTD').hide();

     // Show the loading screen
     $('#loadingScreen').fadeIn();
}
