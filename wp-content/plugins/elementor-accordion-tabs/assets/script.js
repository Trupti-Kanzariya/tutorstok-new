jQuery(document).ready(function ($) {

    $(document).on('click', '.apply-btn', function (e) {
            e.preventDefault(); // Prevent default if needed
            $('body').addClass('add-remove-webinar-cls');

        });

    $('form.wpcf7-form').attr('novalidate', 'novalidate');

    $.validator.addMethod("strict_email", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value);
    }, "Enter a valid email address.");



    $.validator.addMethod('filetype', function (value, element, param) {
        if (element.files.length === 0) return true;
        const file = element.files[0];
        return param.includes(file.type);
    }, 'Invalid file type.');

    $.validator.addMethod('filesize', function (value, element, param) {
        if (element.files.length === 0) return true;
        const file = element.files[0];
        return file.size <= param;
    }, 'File size must be less than 2MB.');

    let validator;

    $('form.wpcf7-form').each(function () {
        validator = $(this).validate({
            rules: {
                'your-email': {
                    required: true,
                    strict_email: true
                },
                'file-116': {
                    required: true,
                    filetype: [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ],
                    filesize: 2097152
                }

            },
            messages: {
                'your-email': {
                   required: "Please enter email address.",
                   strict_email: "Enter a valid email."
               },
               'file-116': {
                required: "Please upload the file.",
                filetype: "Only PDF, DOC, or DOCX files are allowed.",
                filesize: "File must be less than 2MB."
            }
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
              $(element).next('.custom-error').remove();
              element.next('.custom-error').remove();
              error.addClass('custom-error');
              error.insertAfter(element);
          }
        });
    });


    document.addEventListener('wpcf7submit', function (event) {
        const $form = $(event.target);
        if (!validator.form()) {
            event.preventDefault(); 
            return false;
        }
    }, true); 


    $('#file-upload').on('change', function () {
        const fileName = this.files.length > 0 ? this.files[0].name : '';
        $('#file-name-preview').text(fileName);
    });


    document.addEventListener('wpcf7beforesubmit', function (event) {
        const $form = $(event.target);
    const $wrapper = $form.closest('.modal-body'); // or use a more specific class
    $wrapper.find('.form-loader').show();
    $wrapper.find('.apply-now').prop('disabled', true);
}, false);



    document.addEventListener('wpcf7mailsent', function (event) {
        const $form = $(event.target);
        const $wrapper = $form.closest('.modal-body');
        $wrapper.find('.form-loader').hide();
        $wrapper.find('.apply-now').prop('disabled', false);
    }, false);

    document.addEventListener('wpcf7invalid', function (event) {
        const $form = $(event.target);
        const $wrapper = $form.closest('.modal-body');
        $wrapper.find('.form-loader').hide();
        $wrapper.find('.apply-now').prop('disabled', false);
    }, false);

    $('#modalClose, .modal-close').on('click', function () {
        document.body.classList.remove('add-remove-webinar-cls'); // Important

        const $form = $('#modal').find('form.wpcf7-form');
        if ($form.length) {
            $form[0].reset();
            $form.show();
            $form.find('input, textarea, button').prop('disabled', false);
            $form.find('.thank-you-message').remove();
            $form.find('.form-loader').hide();
            $('#file-name-preview').text('').hide();
            $('#your-email-error').text('').hide();
            $('#file-upload-error').text('').hide();
        }
    });
    $(document).on('click', function (event) {
        const $modal = $('#modal');
        const $target = $(event.target);

        // If the click is outside the modal content or modal itself
        if ($modal.is(':visible') && !$target.closest('.modal-content').length && !$target.closest('.modal').length) {
            const $form = $modal.find('form.wpcf7-form');
            if ($form.length) {
                $form[0].reset();
                $form.show();
                $form.find('input, textarea, button').prop('disabled', false);
                $form.find('.thank-you-message').remove();
                $form.find('.form-loader').hide();
                $('#file-name-preview').text('').hide();
                $('#your-email-error').text('').hide();
                $('#file-upload-error').text('').hide();
            }
        }
    });

    // Optional: Reset the form on modal close as well
    $('#modalClose, .modal-close').on('click', function () {
        document.body.classList.remove('add-remove-webinar-cls'); // Important

        const $form = $('#modal').find('form.wpcf7-form');
        if ($form.length) {
            $form[0].reset();
            $form.show();
            $form.find('input, textarea, button').prop('disabled', false);
            $form.find('.thank-you-message').remove();
            $form.find('.form-loader').hide();
            $('#file-name-preview').text('').hide();
            $('#your-email-error').text('').hide();
            $('#file-upload-error').text('').hide();
        }
    });
});

const accordionItems = document.querySelectorAll(".accordion-item");
accordionItems.forEach((item) => {
  const header = item.querySelector(".accordion-header");
  const content = item.querySelector(".accordion-content");
  const icon = item.querySelector(".accordion-icon");

  header.addEventListener("click", () => {
     if (item.classList.contains("open")) {
        item.classList.remove("open");
        content.classList.remove("open");
        if (icon) icon.textContent = "+";
    } else {

        accordionItems.forEach((otherItem) => {
           if (otherItem !== item) {
              otherItem.classList.remove("open");
              const otherContent =
              otherItem.querySelector(".accordion-content");
              const otherIcon = otherItem.querySelector(".accordion-icon");
              if (otherContent) otherContent.classList.remove("open");
              if (otherIcon) otherIcon.textContent = "+";
          }
      });

        item.classList.add("open");
        content.classList.add("open");
        if (icon) icon.textContent = "–";
    }
});
});

const modal = document.getElementById("modal");
const modalClose = document.getElementById("modalClose");
const applyButtons = document.querySelectorAll(".apply-btn");

applyButtons.forEach((btn) => {
  btn.addEventListener("click", (e) => {
    e.stopPropagation();
    document.body.classList.add('add-remove-webinar-cls'); // Final Correct Place
    const formWrapper = document.querySelector('.newmodal-popup-login');
    let existingMessage = formWrapper.querySelector('.thank-you-message');
    if (!existingMessage) {
      existingMessage = document.createElement('div');
      existingMessage.className = 'thank-you-message';
      formWrapper.appendChild(existingMessage);
    }
    existingMessage.textContent = ''; // Clear message
    existingMessage.style.display = 'none'; // Hide on every open
    modal.style.display = "flex";  
    modal.classList.add("active");
  });
});


modalClose.addEventListener("click", () => {
  modal.style.display = "none";   
  modal.classList.remove("active");
});

modal.addEventListener("click", (e) => {
  if (e.target === modal) {
    modal.style.display = "none";  
    modal.classList.remove("active");
}
});


document.addEventListener('DOMContentLoaded', function () {
  const observer = new MutationObserver(function () {
    const fileInput = document.getElementById('file-upload');
    const filePreview = document.getElementById('file-name-preview');

    if (fileInput && filePreview && !fileInput.dataset.listenerAttached) {
      fileInput.addEventListener('change', function () {
        const fileName = fileInput.files.length > 0 ? fileInput.files[0].name : 'No file selected';
        filePreview.textContent = fileName;
        filePreview.style.display = 'block'; // Show it in case it was hidden
    });
      fileInput.dataset.listenerAttached = true;
  }
});

  observer.observe(document.body, { childList: true, subtree: true });
});
jQuery(document).ready(function ($) {
    $(document).on('mouseup', function (e) {
      const $modal = $('#modal');
      const $modalBody = $modal.find('.modal-body');

      if ($modal.is(':visible') && !$modalBody.is(e.target) && $modalBody.has(e.target).length === 0) {
        const $form = $modal.find('form.wpcf7-form');

        if ($form.length) {
          $form[0].reset();
          $form.show();
          $form.find('input, textarea, button').prop('disabled', false);
          $form.find('.thank-you-message').remove();
          $form.find('.form-loader').hide();

          // Clear and hide file name preview
          $('#file-name-preview').text('').css('display', 'none');
          $('#your-email-error').text('').hide();
          $('#file-upload-error').text('').hide();
      }

        $modal.hide(); // Optional: hide modal
    }
});
});
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('writeReviewModal');
    if (window.location.hash === '#review-submitted') {
        modal.style.display = 'none'; // Close modal on reload
        window.scrollTo(0, document.querySelector('.instructor-summary-box').offsetTop); // Scroll to section
    }
});

function openReviewModal() {
    document.getElementById("writeReviewModal").style.display = "flex";
}
function closeReviewModal() {
    document.getElementById("writeReviewModal").style.display = "none";
}

window.onclick = function(event) {
    const modal = document.getElementById("writeReviewModal");
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
document.addEventListener('DOMContentLoaded', function () {
  document.addEventListener('wpcf7mailsent', function (event) {


    // Optional: Check for specific form ID
    // if (event.target.id === 'wpcf7-f21089-p19888-o1') {

    const formWrapper = document.querySelector('.newmodal-popup-login');


    if (formWrapper) {
        const $form = jQuery(event.target);
        $form.hide();

        let message = formWrapper.querySelector('.thank-you-message');
        if (message) {
            message.textContent = 'Thank you! Your message has been sent successfully.';
            message.style.display = 'block';
        } else {
            message = document.createElement('div');
            message.className = 'thank-you-message';
            message.textContent = 'Thank you! Your message has been sent successfully.';
            formWrapper.appendChild(message);
        }
    }
    document.body.classList.remove('add-remove-webinar-cls');
    // }
}, false);
});

jQuery(document).ready(function ($) {
    $('form.wpcf7-form').attr('novalidate', 'novalidate');

    $.validator.addMethod("strict_email", function (value, element) {
        return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value);
    }, "Enter a valid email address.");



    $.validator.addMethod('filetype', function (value, element, param) {
        if (element.files.length === 0) return true;
        const file = element.files[0];
        return param.includes(file.type);
    }, 'Invalid file type.');

    $.validator.addMethod('filesize', function (value, element, param) {
        if (element.files.length === 0) return true;
        const file = element.files[0];
        return file.size <= param;
    }, 'File size must be less than 2MB.');

    let validator;

    $('form.wpcf7-form').each(function () {
        validator = $(this).validate({
            rules: {
                'your-email': {
                    required: true,
                    strict_email: true
                },
                'file-116': {
                    required: true,
                    filetype: [
                        'application/pdf',
                        'application/msword',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                    ],
                    filesize: 2097152
                }

            },
            messages: {
                'your-email': {
                   required: "Please enter email address.",
                   strict_email: "Enter a valid email."
               },
               'file-116': {
                required: "Please upload the file.",
                filetype: "Only PDF, DOC, or DOCX files are allowed.",
                filesize: "File must be less than 2MB."
            }
        },
        errorPlacement: function (error, element) {
            error.insertAfter(element);
        }
    });
    });


    document.addEventListener('wpcf7submit', function (event) {
        const $form = $(event.target);
        if (!validator.form()) {
            event.preventDefault(); 
            return false;
        }
    }, true); 


    $('#file-upload').on('change', function () {
        const fileName = this.files.length > 0 ? this.files[0].name : '';
        $('#file-name-preview').text(fileName);
    });


    document.addEventListener('wpcf7beforesubmit', function (event) {
        const $form = $(event.target);
    const $wrapper = $form.closest('.modal-body'); // or use a more specific class
    $wrapper.find('.form-loader').show();
    $wrapper.find('.apply-now').prop('disabled', true);
}, false);



    document.addEventListener('wpcf7mailsent', function (event) {
        const $form = $(event.target);
        const $wrapper = $form.closest('.modal-body');
        $wrapper.find('.form-loader').hide();
        $wrapper.find('.apply-now').prop('disabled', false);
    }, false);

    document.addEventListener('wpcf7invalid', function (event) {
        const $form = $(event.target);
        const $wrapper = $form.closest('.modal-body');
        $wrapper.find('.form-loader').hide();
        $wrapper.find('.apply-now').prop('disabled', false);
    }, false);

    $('#modalClose, .modal-close').on('click', function () {
        document.body.classList.remove('add-remove-webinar-cls'); // Important

        const $form = $('#modal').find('form.wpcf7-form');
        if ($form.length) {
            $form[0].reset();
            $form.show();
            $form.find('input, textarea, button').prop('disabled', false);
            $form.find('.thank-you-message').remove();
            $form.find('.form-loader').hide();
            $('#file-name-preview').text('').hide();
            $('#your-email-error').text('').hide();
            $('#file-upload-error').text('').hide();
        }
    });
    $(document).on('click', function (event) {
        const $modal = $('#modal');
        const $target = $(event.target);

        // If the click is outside the modal content or modal itself
        if ($modal.is(':visible') && !$target.closest('.modal-content').length && !$target.closest('.modal').length) {
            const $form = $modal.find('form.wpcf7-form');
            if ($form.length) {
                $form[0].reset();
                $form.show();
                $form.find('input, textarea, button').prop('disabled', false);
                $form.find('.thank-you-message').remove();
                $form.find('.form-loader').hide();
                $('#file-name-preview').text('').hide();
                $('#your-email-error').text('').hide();
                $('#file-upload-error').text('').hide();
            }
        }
    });

    // Optional: Reset the form on modal close as well
    $('#modalClose, .modal-close').on('click', function () {
        document.body.classList.remove('add-remove-webinar-cls'); // Important
        const $form = $('#modal').find('form.wpcf7-form');
        if ($form.length) {
            $form[0].reset();
            $form.show();
            $form.find('input, textarea, button').prop('disabled', false);
            $form.find('.thank-you-message').remove();
            $form.find('.form-loader').hide();
            $('#file-name-preview').text('').hide();
            $('#your-email-error').text('').hide();
            $('#file-upload-error').text('').hide();
        }
    });
});
