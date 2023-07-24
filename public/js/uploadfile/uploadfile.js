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

    console.log('test file');
});


function loadingLoader() {
    $('#uploadCashback').hide();

     // Show the loading screen
     $('#loadingScreen').fadeIn();
    console.log('test');
}
