"use strict";

document.addEventListener("DOMContentLoaded", () => {
  flatpickr("#lot-date", {
    enableTime: false,
    dateFormat: "Y-m-d",
    locale: "ru",
  });

  // Display Input File Image
  const updateImageSrc = () => {
    const fileInputs = document.querySelectorAll('input[type="file"]');

    fileInputs.forEach((fileInput) => {
      fileInput.addEventListener("change", (evt) => {
        // Check type
        const file = evt.target?.files?.[0];

        if (file && file.type.startsWith("image")) {
          // Create URL
          const imgUrl = URL.createObjectURL(file);

          // Get image node
          const image = fileInput.previousElementSibling;

          if (image) {
            // Set src
            image.src = imgUrl;
          }
        } else {
          fileInput.value = null;

          alert("File is not image!");
        }
      });
    });
  };

  updateImageSrc();
});
