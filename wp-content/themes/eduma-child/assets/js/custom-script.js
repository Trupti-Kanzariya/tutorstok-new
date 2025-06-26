jQuery(document).ready(function($) {
	$.validator.addMethod("strict_email", function (value, element) {
		return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value);
	}, "Enter a valid email address.");
	$('#mc4wp-form-1').validate({
		rules: {
			EMAIL: {
				required: true,
				strict_email: true
			}
		},
		messages: {
			EMAIL: {
				required: "Email is required.",
				strict_email: "Please enter a valid email address."
			}
		},
		errorElement: 'ul',
		errorPlacement: function(error, element) {
			error.addClass('custom-error-message');
			error.insertAfter(element);
		}
	});
     // Add custom method to allow only letters and spaces for name
	$.validator.addMethod("lettersOnly", function(value, element) {
		return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
	}, "Only letters are allowed");
	$.validator.addMethod("strict_email", function (value, element) {
		return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value);
	}, "Enter a valid email address.");
	$('form.wpcf7-form').validate({
		rules: {
			'your-name': {
				required: true,
				lettersOnly: true
			},
			'your-email': {
				required: true,
				strict_email: true
			},
			'your-subject': {
				required: true
			},
			'your-message': {
				required: true,
                minlength: 10 // Optional: set a minimum message length
            }
        },
        messages: {
        	'your-name': {
        		required: "Please enter your name",
        		lettersOnly: "Only letters are allowed"
        	},
        	'your-email': {
        		required: "Please enter your email",
        		strict_email: "Enter a valid email."
        	},
        	'your-subject': {
        		required: "Please enter a subject"
        	},
        	'your-message': {
        		required: "Please enter your message",
        		minlength: "Message must be at least 10 characters long"
        	}
        },
        errorPlacement: function (error, element) {
        	error.insertAfter(element);
        },
        submitHandler: function(form) {
        	$('.form-loader').fadeIn();
        	form.submit();
        }
    });
	document.addEventListener('wpcf7beforesubmit', function () {
		$('.form-loader').fadeIn();
	});

    // Hide loader after submission is successful
	document.addEventListener('wpcf7mailsent', function () {
		$('.form-loader').fadeOut();
	});

    // Hide loader if submission fails
	document.addEventListener('wpcf7mailfailed', function () {
		$('.form-loader').fadeOut();
	});

	const urlParams = new URLSearchParams(window.location.search);
	const action = urlParams.get('action');

	if (action === 'lostpassword') {
        // Remove the "Login" heading
		$('.elementor-element-1784bac').remove();

        // Remove the "Please enter your details..." paragraph
		$('.elementor-element-9950103').remove();

		$('.elementor-widget-container:contains("New to the platform?")').each(function () {
			if ($(this).text().includes('New to the platform?')) {
				$(this).html('<a href="/tutorstok/user-login/">Back to Login</a>');
			}
		});
	}


// 	$('#lostpasswordform1').on('submit', function (e) {
//     e.preventDefault(); // Always prevent first
//     let isValid = true;
//     $('.form-error').remove(); // Clear old errors

//     const loginField = $('#forgot-pass');
//     const userValue = loginField.val().trim();

//     function showError(field, message) {
//     	isValid = false;
//     	$('<div class="form-error" style="color:red; font-size:13px;">' + message + '</div>').insertAfter(field);
//     }

//     if (!userValue) {
//     	showError(loginField, 'Please enter email.');
//     	return;
//     }

//     if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(userValue)) {
//     	showError(loginField, 'Enter a valid email address.');
//     	return;
//     }


//     $.ajax({
//     	url: my_ajax_object.ajax_url,
//     	type: 'POST',
//     	data: {
//     		action: 'check_user_email_exists',
//     		user_login: userValue
//     	},
//     	success: function (response) {
//     		if (response.exists) {

//     			$('#lostpasswordform1')[0].submit();
//     		} else {
//     			showError(loginField, 'This email is not registered.');
//     		}
//     	}
//     });
// });

jQuery(document).ready(function ($) {
    $('#lostpasswordform1').on('submit', function (e) {
        e.preventDefault();

        let isValid = true;
        $('.form-error').remove();

        const loginField = $('#forgot-pass');
        const userValue = loginField.val().trim();

        function showError(field, message) {
            isValid = false;
            $('<div class="form-error" style="color:red; font-size:13px;">' + message + '</div>').insertAfter(field);
        }

        if (!userValue) {
            showError(loginField, 'Please enter email.');
            return;
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(userValue)) {
            showError(loginField, 'Enter a valid email address.');
            return;
        }

        console.log('Submitting AJAX request to:', my_ajax_object.ajaxurl);

        $.ajax({
            url: my_ajax_object.ajaxurl,
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'check_user_email_exists',
                user_login: userValue
            },
            success: function (response) {
                console.log('AJAX response:', response);
                if (response.exists) {
                    $('#lostpasswordform1')[0].submit();
                } else {
                    showError(loginField, 'This email is not registered.');
                }
            },
            error: function (xhr, status, error) {
                console.log('AJAX error:', error);
                showError(loginField, 'AJAX request failed.');
            }
        });
    });
});


	function transformQuizFilters() {
		$('.learn-press-filters.quiz-filter-types').each(function () {
			var $filterWrapper = $(this);

			if ($filterWrapper.prop('tagName').toLowerCase() === 'ul') return;

			var $newUl = $('<ul class="learn-press-tabs__nav learn-press-filters quiz-filter-types lp-sub-menu"></ul>');

			$filterWrapper.find('li').each(function () {
				var $li = $(this);
				var $span = $li.find('span');
				var filter = $span.data('filter');
				var text = $span.text().trim();
				var isActive = $li.hasClass('active');

				var $newSpan = $('<span class="assignment-filter-tab"></span>')
				.attr('data-filter', filter)
				.text(text);

				if (isActive) $newSpan.addClass('active');

				var $newLi = $('<li class="learn-press-tabs__item"></li>').append($newSpan);
				if (isActive) $newLi.addClass('active');

				$newUl.append($newLi);
			});

			$filterWrapper.replaceWith($newUl);
		});
	}

	function bindFilterClick() {
		$(document).on('click', '.assignment-filter-tab', function () {
			var filter = $(this).data('filter');
			var $container = $(this).closest('.lp-target');
			var sendData = $container.data('send');

			if (sendData && sendData.args) {
				sendData.args.type = filter;
				sendData.args.paged = 1;

				$(this).closest('ul').find('li, span').removeClass('active');
				$(this).addClass('active');
				$(this).parent().addClass('active');

				window.LP.Request.load($container, sendData);
			}
		});
	}

    // Observe changes to .lp-target containers
	function observeTargetChanges() {
		$('.lp-target').each(function () {
			var targetNode = this;

			new MutationObserver(function () {
				transformQuizFilters();
			}).observe(targetNode, {
				childList: true,
				subtree: true,
			});
		});
	}

	transformQuizFilters();
	bindFilterClick();
	observeTargetChanges();

	$('li.logout a').addClass('logout-link').attr('data-logout-url', function() {
		return $(this).attr('href');
	});

	$(document).on('click', '.logout-link', function(e) {
		e.preventDefault();
		$('#logout-confirm-modal').css('display', 'flex').hide().fadeIn();
	});

	$('#confirm-logout').on('click', function() {
		window.location.href = $('.logout-link').attr('href');
	});

	$('#cancel-logout, #modal-close-button').on('click', function() {
		$('#logout-confirm-modal').fadeOut();
	});

	$('#logout-confirm-modal').on('click', function(e) {
		if ($(e.target).is('#logout-confirm-modal')) {
			$(this).fadeOut();
		}
	});
	
});

document.addEventListener('DOMContentLoaded', function () {
	let startTime = "";
	let endTime = "";

	const combinedInput = document.getElementById("combined_time_range");

    // First picker – Start Time
	const startPicker = flatpickr("#start_time_hidden", {
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		time_24hr: true,
		position: "top",
		onClose: function(selectedDates, dateStr) {
			startTime = dateStr;

            // Automatically open end time picker
			setTimeout(() => {
                endPicker.set('minTime', dateStr); // optional
                endPicker.open();
            }, 100);
		}
	});

    // Second picker – End Time
	const endPicker = flatpickr("#end_time_hidden", {
		enableTime: true,
		noCalendar: true,
		dateFormat: "H:i",
		time_24hr: true,
		position: "top",
		onClose: function(selectedDates, dateStr) {
			endTime = dateStr;

			if (startTime && endTime) {
				combinedInput.value = `${startTime} to ${endTime}`;
			}
		}
	});

    // When the combined input is clicked, trigger the first picker
    // combinedInput.addEventListener('click', function () {
    //     startPicker.open();
    // });
});




document.addEventListener('DOMContentLoaded', function () {
	const stars = document.querySelectorAll('.star-rating span');
	const ratingInput = document.getElementById('rating');

	stars.forEach(star => {
		star.addEventListener('click', function () {
			const rating = this.getAttribute('data-value');
			ratingInput.value = rating;

            // Highlight stars
			stars.forEach(s => s.classList.remove('selected'));
			this.classList.add('selected');
			let prev = this.previousElementSibling;
			while (prev) {
				prev.classList.add('selected');
				prev = prev.previousElementSibling;
			}
		});
	});
});


document.addEventListener("DOMContentLoaded", function () {
	if (window.location.href.includes('/tests/')) {

		function addAssignmentHeader() {
			const quizTable = document.querySelector('.profile-list-quizzes');
			if (quizTable && !document.querySelector('.lp-assignment-header')) {
				const headerHTML = `
					<div class="lp-assignment-header">
						<h3 class="lp-assignment-title">Test List</h3>
						<div class="lp-assignment-search">
							<form id="test-search-form" onsubmit="return false;">
								<input type="text" class="test_search_input" name="search" placeholder="Search by test name">
								<button class="test_search" type="submit">Search</button>
							</form>
						</div>
					</div>
				`;
				quizTable.closest('.lp-list-table').insertAdjacentHTML('beforebegin', headerHTML);
			}
		}

		function setupSearchFunctionality() {
		    const searchInput = document.querySelector('.test_search_input');
		    const searchButton = document.querySelector('.test_search');
		    const tableBody = document.querySelector('.profile-list-quizzes tbody');

		    if (searchInput && searchButton && tableBody) {
		        // Hide the existing "No test found" row initially
		        const existingNoDataRow = document.querySelector('.no-test-found');
		        if (existingNoDataRow) {
		            existingNoDataRow.style.display = 'none';
		        }

		        searchButton.addEventListener('click', function(e) {
		            e.preventDefault();
		            const query = searchInput.value.toLowerCase().trim();
		            const rows = tableBody.querySelectorAll('tr:not(.no-test-found)');
		            let visibleCount = 0;

		            // Always remove any previously created message row
		            const newNoDataRow = tableBody.querySelector('.no-test-found');
		            if (newNoDataRow) {
		                newNoDataRow.remove();
		            }

		            // Show all rows if search is empty
		            if (query === '') {
		                rows.forEach(row => {
		                    row.style.display = '';
		                });
		                return;
		            }

		            // Filter rows based on search query
		            rows.forEach(row => {
		                const testName = row.querySelector('td a')?.textContent.toLowerCase() || '';
		                if (testName.includes(query)) {
		                    row.style.display = '';
		                    visibleCount++;
		                } else {
		                    row.style.display = 'none';
		                }
		            });

		            // Show "No test found" only if no matches
		            if (visibleCount === 0) {
		                const noDataRow = document.createElement('tr');
		                noDataRow.className = 'no-test-found';
		                noDataRow.innerHTML = `<td colspan="5" style="text-align: center;">No test found</td>`;
		                tableBody.appendChild(noDataRow);
		            }
		        });
		    }
		}
		addAssignmentHeader();

		const observer = new MutationObserver(function () {
			addAssignmentHeader();
			setupSearchFunctionality();
		});

		observer.observe(document.body, {
			childList: true,
			subtree: true
		});

		document.querySelectorAll('.learn-press-tabs li').forEach(tab => {
			tab.addEventListener('click', function () {
				setTimeout(() => {
					addAssignmentHeader();
					setupSearchFunctionality();
				}, 500);
			});
		});

		setupSearchFunctionality(); // Initial run
	}
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
jQuery(document).on('click', '.test_search', function(e){
	e.preventDefault();
	var search = jQuery('.test_search_input').val().toLowerCase().trim();

    jQuery('.profile-list-table.profile-list-quizzes tbody tr').each(function() {
        var title = jQuery(this).find('td:first-child a').text().toLowerCase().trim();

        if (title.includes(search)) {
            jQuery(this).show();
        } else {
            jQuery(this).hide();
        }
    });
});

// document.addEventListener("DOMContentLoaded", function () {
//     // Try every 300ms up to 5s to wait for AJAX-loaded content
//     const interval = setInterval(() => {
//         const table = document.querySelector(".lp-list-table.profile-list-quizzes");
//         if (table) {
//             clearInterval(interval);
//             profile_quizzes_list_addTab(table);
//         }
//     }, 300);

//     function profile_quizzes_list_addTab(table) {
//         const headerRow = table.querySelector("thead tr");

//         // Avoid duplicate header
//         if (!headerRow.querySelector("th.tutor-name")) {
//             const tutorHeader = document.createElement("th");
//             tutorHeader.classList.add("tutor-name");
//             tutorHeader.textContent = "Tutor Name";
//             headerRow.appendChild(tutorHeader);
//         }

//         const rows = table.querySelectorAll("tbody tr");
//         rows.forEach((row) => {
//             // Avoid duplicate cells
//             if (row.querySelector("td.tutor-name")) return;

//             const courseLink = row.querySelector("td a")?.href || '';
//             const courseSlug = courseLink.split("/courses/")[1]?.split("/")[0] || "";

//             // Set default tutor name (replace this logic with real data if available)
//             let tutorName = "John Doe";

//             const td = document.createElement("td");
//             td.classList.add("tutor-name");
//             td.textContent = tutorName;
//             row.appendChild(td);
//         });
//     }
// });

document.addEventListener("DOMContentLoaded", function () {
    const interval = setInterval(() => {
        const table = document.querySelector(".lp-list-table.profile-list-quizzes");
        if (table) {
            clearInterval(interval);
            profile_quizzes_list_addTab(table);
        }
    }, 300);

    function getBaseUrl() {
        const { origin, pathname } = window.location;
        const pathParts = pathname.split('/');
        const base = pathParts[1]; // e.g., 'tutorstok-stage'
        return `${origin}/${base}`;
    }

    function profile_quizzes_list_addTab(table) {
        const headerRow = table.querySelector("thead tr");

        if (!headerRow.querySelector("th.tutor-name")) {
            const tutorHeader = document.createElement("th");
            tutorHeader.classList.add("tutor-name");
            tutorHeader.textContent = "Tutor Name";
            headerRow.appendChild(tutorHeader);
        }

        const baseUrl = getBaseUrl();

        const rows = table.querySelectorAll("tbody tr");
        rows.forEach((row) => {
            if (row.querySelector("td.tutor-name")) return;

            const courseLink = row.querySelector("td a")?.href || '';
            const courseSlug = courseLink.split("/courses/")[1]?.split("/")[0] || "";

            const td = document.createElement("td");
            td.classList.add("tutor-name");
            td.textContent = "Loading...";
            row.appendChild(td);

            if (courseSlug) {
                fetch(`${baseUrl}/wp-json/custom/v1/tutor-name?slug=${courseSlug}`)
                    .then(res => res.json())
                    .then(data => {
                        td.textContent = data.tutor_name || "Tutor not found";
                    })
                    .catch(() => {
                        td.textContent = "Tutor not found";
                    });
            } else {
                td.textContent = "Tutor not available";
            }
        });
    }
});

jQuery(document).ready(function($) {
  var $modal    = $('#custom-video-modal'),
      $body     = $modal.find('.cv-modal__body'),
      $close    = $modal.find('.cv-modal__close'),
      $overlay  = $modal.find('.cv-modal__overlay');

  // 1) Open modal when any .video_testimonial_main is clicked
  $(document).on('click', '.video_testimonial_main', function(e){
    e.preventDefault();

    const $clickedBox     = $(this);
    const $videoContainer = $clickedBox.find('.video_testimonial');
    const videoURL        = $videoContainer.find('a').attr('href');

    const $profileContent = $clickedBox.find('.client-profile-wrap').clone(); 

    // Combine video and client profile HTML
   const videoHTML = `
	<div class="modal-video-wrapper">
		<video id="custom-video" autoplay loop controls>
		<source src="${videoURL}" type="video/mp4">
		Your browser does not support HTML5 video.
		</video>
	</div>
	`;


    $body.html(videoHTML).append($profileContent);

    $modal.fadeIn(200);
  });

  // 2) Close modal and cleanup
  function closeModal(){
    $modal.fadeOut(200, function(){
      $body.empty();
    });
  }

  $close.on('click', closeModal);
  $overlay.on('click', closeModal);

  // 3) Esc key closes modal
  $(document).on('keydown', function(e){
    if ( e.key === 'Escape' ) {
      closeModal();
    }
  });
});

jQuery(document).ready(function($) {
    if ($('.course-tab-panel-curriculum .lp-course-curriculum').length === 0) {
        $('#tab-curriculum').hide(); // hide the tab
    }
});
// document.addEventListener('DOMContentLoaded', function() {

//     // Add class when 'Add Webinar' button is clicked
//     document.getElementById('add-webinar-btn').addEventListener('click', function() {
//         document.body.classList.add('add-remove-webinar-cls');
//     });

//     // Add class when any 'Edit Webinar' button is clicked
//     document.querySelectorAll('.edit-webinar-btn').forEach(function(button) {
//         button.addEventListener('click', function() {
//             document.body.classList.add('add-remove-webinar-cls');
//         });
//     });

//     // Remove class when 'Save', 'Cancel', or 'Close' button is clicked
//     document.getElementById('save-webinar-btn').addEventListener('click', function() {
//         document.body.classList.remove('add-remove-webinar-cls');
//     });

//     document.getElementById('close-webinar-modal').addEventListener('click', function() {
//         document.body.classList.remove('add-remove-webinar-cls');
//     });

//     document.getElementById('modal-close-button-closed-modal').addEventListener('click', function() {
//         document.body.classList.remove('add-remove-webinar-cls');
//     });

// });


document.addEventListener('DOMContentLoaded', function() {

    // Add class when 'Add Webinar' button is clicked
    document.getElementById('add-webinar-btn').addEventListener('click', function() {
        document.body.classList.add('add-remove-webinar-cls');
    });

    // Add class when any 'Edit Webinar' button is clicked
    document.querySelectorAll('.edit-webinar-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            document.body.classList.add('add-remove-webinar-cls');
        });
    });

    // Add class when 'Apply Now' open button is clicked
    document.addEventListener('click', function(e) {
        if (e.target.closest('.apply-btn')) {
            document.body.classList.add('add-remove-webinar-cls');
            console.log('Class added to body'); // For your confirmation
        }
    });

    // Remove class when 'Save', 'Cancel', 'Modal Close', or 'Apply Now' submit button is clicked
    document.getElementById('save-webinar-btn').addEventListener('click', function() {
        document.body.classList.remove('add-remove-webinar-cls');
    });

    document.getElementById('close-webinar-modal').addEventListener('click', function() {
        document.body.classList.remove('add-remove-webinar-cls');
    });

    document.getElementById('modal-close-button-closed-modal').addEventListener('click', function() {
        document.body.classList.remove('add-remove-webinar-cls');
    });

    document.getElementById('modalClose').addEventListener('click', function() {
        document.body.classList.remove('add-remove-webinar-cls');
    });

    document.querySelectorAll('.apply-now').forEach(function(button) {
        button.addEventListener('click', function() {
            document.body.classList.remove('add-remove-webinar-cls');
        });
    });

});
