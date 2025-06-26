jQuery(document).ready(function ($) {
	// $('input[type="checkbox"], input[type="radio"], select').off('change').on('change', function (e) {
	// 	e.preventDefault();
	// 	e.stopPropagation();
	// });

	// $('.course-filter-submit').on('click', function (e) {
	// 	e.preventDefault();
	// 	const url = new URL(window.location.href);
	// 	const baseUrl = url.origin + url.pathname;
	// 	const params = new URLSearchParams();
	// 	$('input[name="c_authors[]"]:checked').each(function () {
	// 		params.append('c_authors', $(this).val());
	// 	});
	// 	$('input[name="term_id[]"]:checked').each(function () {
	// 		params.append('term_id', $(this).val());
	// 	});
	// 	$('input[name="c_review_star[]"]:checked').each(function () {
	// 		params.append('c_review_star', $(this).val());
	// 	});
	// 	const level = $('input[name="c_level"]:checked').val();
	// 	if (level) {
	// 		params.set('c_level', level);
	// 	}
	// 	const sortBy = $('select[name="sort_by"]').val();
	// 	if (sortBy) {
	// 		params.set('sort_by', sortBy);
	// 	}
	// 	window.location.href = baseUrl + '?' + params.toString();
	// });
	jQuery(document).on('click', '.ajax-pagination-link', function(e) {
		e.preventDefault();
		var page = jQuery(this).data('page');

		jQuery.ajax({
			url: my_ajax_object.ajaxurl,
			type: 'POST',
			dataType: 'json',
			data: {
				action: 'load_questions_page',
				paged: page,
			},
			beforeSend: function() {
				jQuery('#question-table-body').html('<tr><td colspan="5">Loading...</td></tr>');
			},
			success: function(response) {
				if (response.success) {
					jQuery('#question-table-body').html(response.data.html);
					jQuery('.lp-pagination').html(response.data.pagination);
				} else {
					jQuery('#question-table-body').html('<tr><td colspan="5">Error loading data</td></tr>');
				}
			}
		});
	});


  
  $('body').on('click', '.quiz-pagination-link', function (e) {
    e.preventDefault();

    var page = $(this).data('page');
    var perPage = 5;
    console.log('Pagination clicked: page', page);

    $.ajax({
        url: my_ajax_object.ajaxurl,
        type: 'POST',
        data: {
            action: 'load_quizzes_pagination',
            page: page,
            per_page: perPage
        },
        beforeSend: function () {
            $('#quiz-list').addClass('loading');
        },
        success: function (response) {
            console.log(response); // debugging
            if (response.success) {
                $('#quiz-list').html(response.data.html);
                $('.lp-pagination').replaceWith(response.data.pagination);
                $('.quiz-pagination-link').removeClass('current');
                $('.quiz-pagination-link[data-page="' + page + '"]').addClass('current');
            } else {
                console.error('Failed to fetch quizzes.');
            }
        },
        complete: function () {
            $('#quiz-list').removeClass('loading');
        }
    });
});



});



document.querySelectorAll('input[name="c_review_star"]').forEach((el) => {
	el.addEventListener('change', function () {
		const selectedValue = this.value;
		// Perform AJAX or submit form with the selected rating
		filterByRating(selectedValue);
	});
});

function filterByRating(rating) {
	// Replace this with your AJAX or filtering logic
	console.log("Selected rating: ", rating);
	// Example: AJAX fetch for filtered courses
}

// profile form validation
jQuery(document).ready(function ($) {
	 $('#profile_image').on('change', function () {
    const file = this.files[0];
    const previewContainer = $(this).closest('.form-field-input');

    // Remove previous error and file name
    previewContainer.find('.custom-error').remove();
    previewContainer.find('.file-name-label').remove();

    if (file) {
        const fileName = file.name;
        const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.webp)$/i;

        // Validate file extension
        if (!allowedExtensions.test(fileName)) {
            const error = $('<span class="custom-error" style="color:red; font-size:13px;"></span>').text('Only JPG, JPEG, PNG, or WEBP files are allowed.');
            previewContainer.append(error);
            return;
        }

        const reader = new FileReader();
        reader.onload = function (e) {
            let img = previewContainer.find('img.img-choose-file-cls-adding-here');

            if (img.length) {
                img.attr('src', e.target.result); // Replace existing image
            } else {
                // If image doesn't exist for some reason, create a new one
                img = $('<img class="img-choose-file-cls-adding-here" width="100" height="100" style="display:block;margin-top:10px;" />');
                img.attr('src', e.target.result);
                previewContainer.prepend(img);
            }
        };
        reader.readAsDataURL(file);
    }
});

    //alert('hello');

	$('.lp-button[type="submit"]').on('click', function (e) {
		let isValid = true;
		$('.form-error').remove();

		function showError(field, message) {
			isValid = false;
			$('<div class="form-error" style="color:red; font-size:13px;">' + message + '</div>').insertAfter(field);
		}

		const fields = [
        // { selector: '#profile_image', message: 'Profile image is required' },
			{ selector: '#first_name', message: 'First name is required' },
			{ selector: '#last_name', message: 'Last name is required' },
        // { selector: '#billing_phone', message: 'Phone number is required' },
        // { selector: '#birth_date, #birth_date_teacher', message: 'Birth date is required' },
        // { selector: '#grade', message: 'Grade is required' },
        // { selector: '#address', message: 'Address is required' },
        // { selector: '#preferred_subjects', message: 'Preferred subjects are required' },
        // { selector: '#learning_style', message: 'Select your learning style' },
        // { selector: '#description', message: 'Bio is required' },
		];

		fields.forEach(function (field) {
			const el = $(field.selector);
			if (el.length && (!el.val() || el.val().trim() === '')) {
				showError(el, field.message);
			}
		});

		const imageInput = $('#profile_image');
		const file = imageInput.val().trim();
		const existingImage = $('img[alt="Profile Image"]');
		const existingImageSrc = existingImage.attr('src') || '';

		const hasSavedImage = existingImage.length > 0 && existingImageSrc !== '' && !existingImageSrc.includes('default.png');

		if (file) {
			const allowedExtensions = /(\.jpg|\.jpeg|\.png|\.webp)$/i;
			if (!allowedExtensions.exec(file)) {
				showError(imageInput, 'Only JPG, JPEG, PNG, or WEBP files are allowed');
			}
		}




		if (!isValid) {
            e.preventDefault(); // Prevent form from submitting if there are errors
        }
    });


	$.validator.addMethod("strict_email", function (value, element) {
		return this.optional(element) || /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/.test(value);
	}, "Enter a valid email address.");


	$('#loginform1').on('submit', function (e) {
	let isValid = true;
	$('.form-error').remove();

	function showError(field, message) {
		isValid = false;
		$('<div class="form-error" style="color:red; font-size:13px;">' + message + '</div>').insertAfter(field);
	}

	const loginField = $('#user_login');
	const passwordField = $('#user_pass');

	const loginValue = loginField.val().trim();

	if (!loginValue) {
		showError(loginField, 'Please enter email.');
	}

	// password empty
	if (!passwordField.val().trim()) {
		showError(passwordField, 'Please enter password');
	}

	if (!isValid) {
		e.preventDefault();
	}
});


});

jQuery(document).ready(function($) {
	$(document).on('click', '.lp-course-filter__field input[type="checkbox"], .lp-course-filter__field input[type="radio"]', function(e) {
		e.stopImmediatePropagation();
	});
	$('.lp-form-course-filter').on('submit', function(e) {
		const submitter = e.originalEvent?.submitter;
		if (!submitter || !$(submitter).hasClass('course-filter-submit')) {
			e.preventDefault();
		}
	});
});


jQuery(document).ready(function($) {
	$(document).on('click', '.course_filter_tutorstok', function(e) {
		e.preventDefault();
		$('.lp-course-filter-modal').attr('style', 'display: block !important');
	});
	$(document).on('click', '.lp-course-filter-close', function(e) {
		$('#lp-course-filter-popup').css('display', 'none');
	});
	$(window).on('click', function(e) {
		if ($(e.target).is('#lp-course-filter-popup')) {
			console.log('Clicked outside popup');
			$('#lp-course-filter-popup').css('display', 'none');
		}
	});
});

// Open popup logic
document.addEventListener('DOMContentLoaded', function () {
	const popup = document.querySelector('.lp-course-filter-popup');
	const overlay = document.querySelector('.lp-filter-overlay');
	const openBtn = document.querySelector('#open-course-filter');

	if (openBtn) {
		openBtn.addEventListener('click', () => {
			popup.classList.add('active');
			overlay.classList.add('active');
		});
	}

	if (overlay) {
		overlay.addEventListener('click', () => {
			popup.classList.remove('active');
			overlay.classList.remove('active');
		});
	}
});


// Filter popup DOM     
jQuery(document).ready(function () {
	setTimeout(function () {
		const $ = jQuery;
		const formContainer = $('.lp-form-course-filter');
		const filterItems = $('.lp-form-course-filter__item');
		const tabsContainer = $('<div class="tabs"></div>');
		const filterWrapper = $('<div class="filter-wrapper"></div>');
		const containerWrapper = $('<div class="filter-container-wrapper"></div>');
		filterItems.each(function (index) {
			const title = $(this).find('.lp-form-course-filter__title').text();
			const tabButton = $('<button></button>')
			.text(title)
			.attr('data-index', index)
			.on('click', function (e) {
				e.preventDefault();
				setActiveTab($(this).data('index'));
			});

			tabsContainer.append(tabButton);

			$(this).attr('data-tab', index).hide();
			$(this).find('.lp-form-course-filter__content').addClass('tab-content');
		});
		filterItems.add('.lp-course-filter-category').wrapAll(filterWrapper);
		containerWrapper.append(tabsContainer);
		containerWrapper.append($('.filter-wrapper'));
		if (formContainer.length) {
			formContainer.prepend(containerWrapper);
		}
		function setActiveTab(index) {
			$('.lp-form-course-filter__item').hide().removeClass('active');
			const activeItem = $('.lp-form-course-filter__item').eq(index);
			activeItem.show().addClass('active');

			$('.tabs button').removeClass('active').eq(index).addClass('active');
		}
		const submitBtn = formContainer.find('.course-filter-submit').detach();
		const closeBtn = formContainer.find('.lp-form-course-filter__close').detach();
		const resetBtn = $('.course-filter-reset').detach();
		const buttonWrapper = $('<div class="form-action-buttons"></div>')
		.append(submitBtn)
		.append(resetBtn)
		.append(closeBtn);

		formContainer.append(buttonWrapper);
		formContainer.on('submit', function (e) {
			e.preventDefault();
			// console.log('Form submit prevented');
		});
		$('.close_popup').on('click', function () {
			$('.lp-course-filter-popup').removeClass('active');
			$('.lp-filter-overlay').removeClass('active');
		});
		setActiveTab(0);
	}, 1500);
});

jQuery(document).ready(function($) {
	setTimeout(function () {
		var $filterTags = $('.custom-filter-tags');
		var $topbar = $('.thim-ekits-archive-course__topbar');
		if ($filterTags.length && $topbar.length) {
			$filterTags.insertAfter($topbar);
		}
		$('.custom-filter-tags').attr('style','display:block;');
	}, 1000);
});


jQuery(document).ready(function($) {
    // Open modal
	$(document).on('click', '.filter-button', function(e) {
		e.preventDefault();
		$('.filter-modal').attr('style', 'display:flex;');
	});
    // Close modal
	$(document).on('click', '.filter-close', function() {
		$('.filter-modal').attr('style', 'display:none;');
	});
    // Tab switching
	$(document).on('click', '.tablinks', function() {
		var tab = $(this).data('tab');
		$('.tabcontent').hide();
		$('.tablinks').removeClass('active');
        // $('#' + tab).show();
		$('.' + tab).attr('style', 'display:block;');
		$(this).addClass('active');
	});
    // Reset checkboxes
	$(document).on('click', '.reset-button', function() {
		$('#filter-modal input[type="checkbox"]').prop('checked', false);
	});
    // Initialize default tab
	var defaultTab = $('.tablinks.active');
	if (defaultTab.length) {
		var tab = defaultTab.data('tab');
		$('.tabcontent').hide();
		$('#' + tab).show();
	}

    // 5. Initialize default tab
	var defaultTab = $('.tablinks.active');
	if (defaultTab.length) {
		var tab = defaultTab.data('tab');
		$('.tabcontent').hide();
		$('.' + tab).css('display', 'block');
	}
});

// Birthday of student date validation
document.addEventListener('DOMContentLoaded', function () {
	const birthDateInput = document.getElementById('birth_date');
	if (birthDateInput) {
		const today = new Date();
        // Subtract 2 years from today's date
		const maxDate = new Date(today.getFullYear() - 2, today.getMonth(), today.getDate());

        // Format as YYYY-MM-DD
		const maxDateStr = maxDate.toISOString().split('T')[0];

        // Set max attribute to restrict future and under-2-year dates
		birthDateInput.setAttribute('max', maxDateStr);
	}
});

// Birthday of teacher date validation

document.addEventListener('DOMContentLoaded', function () {
	const teacher_birthDateInput = document.getElementById('birth_date_teacher');
	if (teacher_birthDateInput) {
		const today = new Date();
        // Subtract 2 years from today's date
		const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());

        // Format as YYYY-MM-DD
		const maxDateStr = maxDate.toISOString().split('T')[0];

        // Set max attribute to restrict future and under-2-year dates
		teacher_birthDateInput.setAttribute('max', maxDateStr);
	}
});

jQuery(document).ready(function($) {
	$('.mbsc-scroller-wheel-item div > div').each(function() {
		if ($(this).text().trim() === 'TRIAL') {
			$(this).hide();
		}
	});
});



jQuery(document).ready(function($) {
	$('.dashboard-php-cls-adding-here-all').on('click', function(e) {
		console.log('test');
		e.preventDefault();
		$('.profiles').hide();
		$('.basic-information').show();
	});
});


jQuery(document).ready(function($) {
    var stdListHTML = $('.custom_std_list'); // Target the custom_std_list class

    if (stdListHTML.length) {
        var studentsListContainer = $('.course-tab-panel-students-list'); // Target the course-tab-panel-students-list div

        if (studentsListContainer.length) {
            // Replace the content inside the students list container
        	studentsListContainer.html(stdListHTML.html());
        }
    }
});
document.addEventListener("DOMContentLoaded", function() {
	var filterButton = document.getElementById("open-course-filter");
	if (filterButton) {
		filterButton.style.display = "block";
	}
});

jQuery(document).ready(function($) {
	var materialList = $('.tutor_material_list');
	var instructorPanel = $('.course-tab-panel-instructor');
	if (materialList.length && instructorPanel.length) {
		instructorPanel.html(materialList.html());
		materialList.hide();
	}
});

jQuery(document).ready(function($) {
    // $(document).on('click', '.type-submit-cls-adding-here', function(e) {
    //   e.preventDefault();

    //   let data = {
    //     action: 'filter_courses',
    //     progress_range: [],
    //     grade: [],
    //     subject: []
    //   };

    //   $('input[name="progress_range[]"]:checked').each(function() {
    //     data.progress_range.push($(this).val());
    //   });

    //   $('input[name="grade[]"]:checked').each(function() {
    //     data.grade.push($(this).val());
    //   });

    //   $('input[name="subject[]"]:checked').each(function() {
    //     data.subject.push($(this).val());
    //   });

    //   $.ajax({
    //     url: ajaxurl,
    //     type: 'POST',
    //     data: data,
    //     success: function(response) {
    //       $('.lp_profile_course_progress tbody').html(response); // Only replace tbody content
    //       $('#filter-modal').hide(); // Optional
    //     }
    //   });
    // });
	$(document).on('click', '.type-submit-cls-adding-here', function(e) {
		e.preventDefault();
        //  alert('hello');
		var selectedProgress = [];
		$('input[name="progress_range[]"]:checked').each(function() {
			selectedProgress.push($(this).val());
		});

		var selectedGrades = [];
		$('input[name="grade[]"]:checked').each(function() {
			selectedGrades.push($(this).val());
		});

		var selectedSubjects = [];
		$('input[name="subject[]"]:checked').each(function() {
			selectedSubjects.push($(this).val());
		});
		var rowsVisible = false;
		$('.course-row').each(function() {
            // alert('hello2');
			var row = $(this);
			var progress = parseFloat(row.data('progress'));
			var grade = row.data('grade');
			var subject = row.data('subject');

			let showRow = true;

          // Progress filter
			if (selectedProgress.length) {
				showRow = selectedProgress.some(function(range) {
					var parts = range.split('-');
					var min = parseFloat(parts[0]);
					var max = parseFloat(parts[1]);
					return progress >= min && progress < max;
				});
			}

          // Grade filter
			if (showRow && selectedGrades.length && grade) {
				showRow = selectedGrades.includes(grade);
			}
			if (showRow && selectedSubjects.length && subject) {
				showRow = selectedSubjects.includes(subject);
			}

			if (showRow) {
				row.show();
				rowsVisible = true;
			} else {
				row.hide();
			}
		});
		if (rowsVisible) {
			$('.lp_profile_course_progress').show();  
			$('.no-record-message').hide();  
		} else {
			var thCount = $('.lp_profile_course_progress thead th').length;
			$('.no-record-message td').attr('colspan', thCount);
			$('.lp_profile_course_progress').hide();  
			$('.no-record-message').show();  
		}

		$('.filter-modal').attr('style', 'display:none;');
	});
});

// Video slider js
function animateTextBoxes(slide) {
	slide.querySelectorAll(".animate-letters").forEach((el) => {
		if (el.dataset.orig) el.innerHTML = el.dataset.orig;
		const html = el.innerHTML.trim();
		const parts = html.split(/<br\s*\/?>/i);
		el.innerHTML = parts
		.map((part, idx) => {
			const sanitized = part.replace(/\s+/g, " ").trim();
			const chars = Array.from(sanitized);
			let delayOffset = idx * chars.length;
			const letters = chars
			.map((c, i) => {
				const delay = (i + delayOffset) * 0.05;
				return c === " "
				? `<span class="letter" style="--delay:${delay}s">&nbsp;</span>`
				: `<span class="letter" style="--delay:${delay}s">${c}</span>`;
			})
			.join("");
			return (idx > 0 ? "<br/>" : "") + letters;
		})
		.join("");
	});

	slide.querySelectorAll(".animate-words").forEach((el) => {
		if (el.dataset.orig) el.innerHTML = el.dataset.orig;
		const rawHtml = el.innerHTML.trim();
		const parts = rawHtml.split(/<br\s*\/?>/i);

		el.innerHTML = parts
		.map((part, lineIdx) => {
			const text = part.replace(/\s+/g, " ").trim();
			const words = text.split(" ");
			const wordHtml = words
			.map(
				(w, i) =>
				`<span class="word" style="--delay:${
					(lineIdx * 10 + i) * 0.05
				}s">${w}</span>`
				)
			.join(" ");
			return lineIdx > 0 ? `<br>${wordHtml}` : wordHtml;
		})
		.join("");
	});
}

const slideClasses = ["first", "second", "third", "four"];

function updateSlideClasses(swiperInstance) {
	swiperInstance.slides.forEach((slide) => {
		slide.classList.remove(...slideClasses);
		const realIndex = slide.getAttribute("data-swiper-slide-index");
		if (realIndex !== null) {
			slide.classList.add(slideClasses[realIndex % slideClasses.length]);
		}
	});
}


document.addEventListener("DOMContentLoaded", function () {
	if (document.body.classList.contains("home")) {
		const tutorsSwiper = new Swiper(".tutors-slider .swiper", {
			direction: "vertical",
			loop: true,
			speed: 1000,
			autoplay: {
				delay: 4800,
				disableOnInteraction: false,
			},
			effect: "creative",
			creativeEffect: {
				prev: {
					translate: [0, 0, 0],
				},
				next: {
					translate: [0, "100%", 0],
				},
			},
			on: {
				init(swiperInstance) {
					updateSlideClasses(swiperInstance);
				},
				slideChangeTransitionStart() {
					document.querySelectorAll(".tutors-slider .video-box").forEach((v) => {
						v.style.opacity = 0;
					});
					document.querySelectorAll(".tutors-slider .text-box").forEach((t) => {
						t.style.opacity = 0;
						t.style.transform = "translateY(50px)";
					});
				},
				slideChangeTransitionEnd(swiperInstance) {
					updateSlideClasses(swiperInstance);
					const active = document.querySelector(".tutors-slider .swiper-slide-active");
					if (active) {
						active.querySelectorAll(".video-box").forEach((v) => {
							v.style.opacity = 1;
						});
						active.querySelectorAll(".text-box").forEach((t) => {
							t.style.opacity = 1;
							t.style.transform = "translateY(0)";
						});
						animateTextBoxes(active);
					}
				},
			},
		});
	}
});


document.addEventListener("DOMContentLoaded", () => {
	document
	.querySelectorAll(".tutors-slider .animate-letters")
	.forEach((el) => {
		el.dataset.orig = el.innerHTML.trim();
	});
	document
	.querySelectorAll(".tutors-slider .animate-words")
	.forEach((el) => {
		el.dataset.orig = el.innerHTML.trim();
	});

	const first = document.querySelector(
		".tutors-slider .swiper-slide-active"
		);
	if (first) animateTextBoxes(first);
});

    // Search title detail overview
jQuery(document).ready(function ($) {
	$(document).on('click', '.search_course', function (e) {
		e.preventDefault();

		const query = $('#course-search-input').val()?.toLowerCase().trim() || '';

		$('.course-title').each(function () {
			const rawTitle = $(this).text();
			const title = typeof rawTitle === 'string' ? rawTitle.toLowerCase() : '';

			if (query === '' || title.includes(query)) {
                $(this).closest('.course-row').show(); // show the full row
            } else {
                  $(this).closest('.course-row').hide(); // hide the full row
              }
          });
	});
});


jQuery(document).ready(function ($) {
	$('.course-item').each(function () {
		var $item = $(this);
		var itemId = $item.data('item-id');

		if (!itemId) return;

		$.ajax({
			url: my_ajax_object.ajaxurl, 
			type: 'POST',
			data: {
				action: 'get_lesson_duration', 
				item_id: itemId 
			},
			success: function (response) {

				if (response.success && response.data && response.data.duration) {
					console.log(response.data.duration);
					$item.find('.course-item-title').append(' <span class="course-duration">(' + response.data.duration + ')</span>');
				} else {

					if (response.data) {
                        console.log("Duration response data:", response.data); 
                    }
                }
            },
            error: function(xhr, status, error) {
            	console.log('AJAX error:', error); 
            }
        });
	});
});

document.addEventListener("DOMContentLoaded", function () {
  function attachValidation() {
    const form = document.querySelector(".thim-ekits-archive-course__topbar__search");
    if (!form) return;

    form.addEventListener("submit", function (e) {
      const input = form.querySelector('input[name="c_search"]');
      const value = input.value.trim();

      // Remove old message if it exists
      const oldError = form.querySelector(".search-error");
      if (oldError) oldError.remove();

      if (value === "") {
        e.preventDefault(); // Stop submission
        const error = document.createElement("div");
        error.className = "search-error";
        error.style.color = "red";
        error.style.marginTop = "5px";
        // error.textContent = "Please enter a search keyword.";
        form.appendChild(error);
        input.focus();
      }
    });
  }

  // Run initially
  attachValidation();

  // If the theme loads content dynamically, rebind on page changes
  const observer = new MutationObserver(attachValidation);
  observer.observe(document.body, { childList: true, subtree: true });
});

// Wait for the document to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Create a mutation observer to listen for DOM changes
    const observer = new MutationObserver(function(mutationsList, observer) {
        // Look for the button after changes to the DOM
        const button = document.querySelector('button[data-index="1"]');
        
        if (button) {
            button.textContent = "Subjects";
            observer.disconnect();  // Stop observing once the button is found
        }
    });

    // Start observing the document for added nodes
    observer.observe(document.body, { childList: true, subtree: true });
});

document.addEventListener('DOMContentLoaded', function () {
    const loader = document.getElementById('course-filter-loader');
    const applyBtn = document.querySelector('.course-filter-submit');
    const form = document.querySelector('.course-filter-form');

    if (applyBtn && form) {
        // Show loader when Apply button is clicked
        applyBtn.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent the form from submitting immediately

            loader.style.display = 'flex'; // Show loader

            // Submit the form to trigger the filtering action (this will cause a page reload)
            form.submit();
        });
    }
});


jQuery(document).ready(function($) {
	// Handle pagination
	$(document).on('click', '.lp-page-btn', function() {
		var page = $(this).data('page');
		$('#lp-submission-table-content').html('<p>Loading...</p>');

		$.ajax({
			url: my_ajax_object.ajaxurl,
			type: 'POST',
			data: {
				action: 'lp_get_paginated_quiz_submissions',
				page: page
			},
			success: function(response) {
				if (response.success) {
					$('#lp-submission-table-content').html(response.data.table);
				} else {
					$('#lp-submission-table-content').html('<p>Failed to load.</p>');
				}
			}
		});
	});
});
document.addEventListener('DOMContentLoaded', function () {
    const notificationTab = document.querySelector('.learn-press-tabs__item.notification-setting');
    if (notificationTab) {
        notificationTab.style.display = 'none';
    }
});

jQuery(document).ready(function($) {
	$(document).on('click', '.lp-page-btn', function() {
		var page = $(this).data('page');

		// Highlight active button
		$('.lp-page-btn').removeClass('current');
		$(this).addClass('current');

		$.ajax({
			url: my_ajax_object.ajaxurl,
			type: 'POST',
			data: {
				action: 'lp_notifications_pagination',
				page: page
			},
			beforeSend: function() {
				$('#notification-list').html('<p>Loading...</p>');
			},
			success: function(response) {
				$('#notification-list').html(response);

				// Optional: scroll to notification list
				$('html, body').animate({
					scrollTop: $('#notification-list').offset().top - 100
				}, 300);
			}
		});
	});
});
//Course details page student tab searchbox
document.addEventListener("DOMContentLoaded", function () {
        if (window.location.hash === "#tab-students-list") {
            const tab = document.querySelector('#tab-students-list');
            const panelId = tab?.getAttribute('aria-controls');
            const panel = panelId ? document.getElementById(panelId) : null;

            // Deactivate all tabs
            document.querySelectorAll('[role="tab"]').forEach(t => {
                t.setAttribute('aria-selected', 'false');
                t.classList.remove('active');
            });

            // Hide all tab panels
            document.querySelectorAll('[role="tabpanel"]').forEach(p => {
                p.setAttribute('hidden', true);
            });

            // Activate this tab and panel
            if (tab && panel) {
                tab.setAttribute('aria-selected', 'true');
                tab.classList.add('active');
                panel.removeAttribute('hidden');
            }
        }
    });

jQuery(document).ready(function($) {  
    $(document).on('keyup', '#courseSearchInput', function() {
    	let value = $(this).val().toLowerCase();
    	let visibleCount = 0;
    	$('.lp-course-table tbody tr').each(function() {
    		let courseName = $(this).find('td:nth-child(2)').text().toLowerCase();
    		let show = courseName.indexOf(value) > -1;
    		$(this).toggle(show);
    		if (show) visibleCount++;
    	});

    	const noResultsVisible = visibleCount === 0;
    $('#noResultsMessage').toggle(noResultsVisible);
    $('.lp-course-table thead.course-data').toggle(!noResultsVisible);
    });

});

jQuery(document).ready(function($) {
  $(document).on('keyup', '#course-search-input', function() {
    const searchVal = $(this).val().toLowerCase().trim();
    let visibleCount = 0;

    $('.course-row').each(function() {
     const title = $(this).find('.course-title').text().toLowerCase();
      if (title.includes(searchVal)) {
        $(this).show();
        visibleCount++;
      } else {
        $(this).hide();
      }
    });

   if (visibleCount === 0) {
      $('.no-record-message').show();
      $('.lp_profile_course_progress').hide();
    } else {
      $('.no-record-message').hide();
      $('.lp_profile_course_progress').show();
    }
  });
});
document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('file-upload');
    const preview = document.getElementById('file-name-preview');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            preview.innerHTML = ''; // clear previous name
            const file = this.files[0];

            if (file) {
                const fileName = document.createElement('p');
                fileName.textContent = 'Selected file: ' + file.name;
                fileName.style.marginTop = '10px';
                fileName.style.color = '#333';
                preview.appendChild(fileName);
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const fileInput = document.getElementById('assignment_attachment');

    if (fileInput) {
        fileInput.addEventListener('change', function () {
            const allowedExtensions = ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx'];
            const file = fileInput.files[0];

            // Remove previous error message if any
            const existingError = document.querySelector('.file-error-message');
            if (existingError) {
                existingError.remove();
            }

            if (file) {
                const fileExtension = file.name.split('.').pop().toLowerCase();

                if (!allowedExtensions.includes(fileExtension)) {
                    const errorMessage = document.createElement('p');
                    errorMessage.textContent = 'Invalid file type. Only PDF, images, DOC, and DOCX files are allowed.';
                    errorMessage.style.color = 'red';
                    errorMessage.classList.add('file-error-message');

                    fileInput.parentElement.appendChild(errorMessage);
                    fileInput.value = ''; // Clear the invalid file
                }
            }
        });
    }
});


document.addEventListener('DOMContentLoaded', function () {
  const target = document.querySelector('.wp-block-woocommerce-cart');

  if (!target) return;

  const observer = new MutationObserver(function () {
    const itemCountEl = document.querySelector('.total-item-count');
    const items = document.querySelectorAll('.wc-block-cart-items__row');
    const count = items.length;

    // Update count text
    if (itemCountEl) {
      itemCountEl.textContent = count > 0
        ? `${count} ${count === 1 ? 'Course' : 'Courses'} in cart`
        : 'No courses in cart';
    }
  });

  observer.observe(target, {
    childList: true,
    subtree: true,
  });

  // Trigger click event for remove button
  document.addEventListener('click', function (e) {
    const removeBtn = e.target.closest('.wc-block-cart-item__remove-link');
    if (removeBtn) {
      removeBtn.setAttribute('disabled', 'disabled'); // prevent multiple clicks
    }
  });
});


document.addEventListener('click', function (e) {
  const closeBtn = e.target.closest('.close');

  if (closeBtn) {
    const form = closeBtn.closest('form');
    if (form) {
      // Reset title and content
      form.querySelectorAll('input[type="text"], textarea').forEach(el => el.value = '');

      // Reset hidden rating input
      const ratingInput = form.querySelector('input[name="rating"]');
      if (ratingInput) {
        ratingInput.value = 0;
      }

      // Clear visual star highlights (check what class is used, e.g. 'active', 'selected', etc.)
      form.querySelectorAll('.review-stars li span').forEach(star => {
        star.classList.remove('hover');
      });

      // Also remove class from the parent <li> if necessary
      form.querySelectorAll('.review-stars li').forEach(li => {
        li.classList.remove('hover');
      });

      // Clear error messages
      const errorSpan = form.querySelector('.error');
      if (errorSpan) {
        errorSpan.textContent = '';
      }
    }

    e.preventDefault();
  }
});
document.addEventListener('DOMContentLoaded', function () {
    const tabLinks = document.querySelectorAll('.learn-press-filters li a');

    tabLinks.forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();

            // Remove 'active' from all <li> and all <a>
            document.querySelectorAll('.learn-press-filters li').forEach(li => li.classList.remove('active'));
            document.querySelectorAll('.learn-press-filters li a').forEach(a => a.classList.remove('active'));

            // Add 'active' to <li> only
            this.closest('li').classList.add('active');

            // OPTIONAL: if you want to toggle .active on the link too (not usually needed)
            // this.classList.add('active');

            // For debug
            console.log("Clicked tab:", this.getAttribute("data-tab"));
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
  // Only target the login form page
  if (window.location.pathname.includes("/user-login/")) {
    const emailField = document.getElementById("user_login");
    const passwordField = document.getElementById("user_pass");
    const rememberMe = document.getElementById("rememberMe");
    const logError = document.getElementById("log-error");
    const pwdError = document.getElementById("pwd-error");

    // Clear values
    if (emailField) emailField.value = "";
    if (passwordField) passwordField.value = "";
    if (rememberMe) rememberMe.checked = false;

    // Clear error messages
    if (logError) logError.innerText = "";
    if (pwdError) pwdError.innerText = "";
  }
  if (window.location.pathname.includes("/tutor-login/")) {
    const emailField = document.getElementById("user_login");
    const passwordField = document.getElementById("user_pass");
    const rememberMe = document.getElementById("rememberMe");
    const logError = document.getElementById("log-error");
    const pwdError = document.getElementById("pwd-error");

    // Clear values
    if (emailField) emailField.value = "";
    if (passwordField) passwordField.value = "";
    if (rememberMe) rememberMe.checked = false;

    // Clear error messages
    if (logError) logError.innerText = "";
    if (pwdError) pwdError.innerText = "";
  }
});


document.addEventListener("DOMContentLoaded", function () {
	// Wait for DOM and icon to fully load
	setTimeout(function () {
		const toggleButton = document.getElementById("show_pass");
		const passwordInput = document.getElementById("user_pass");

		if (!toggleButton || !passwordInput) return;

		const icon = toggleButton.querySelector("i");

		// Force default icon to fa-eye-slash (password hidden)
		if (icon) {
			icon.classList.remove("fa-eye");
			icon.classList.add("fa-eye-slash");
		}

		// Toggle logic on click
		toggleButton.addEventListener("click", function () {
			if (passwordInput.type === "password") {
				passwordInput.type = "text";
				icon.classList.remove("fa-eye-slash");
				icon.classList.add("fa-eye");
			} else {
				passwordInput.type = "password";
				icon.classList.remove("fa-eye");
				icon.classList.add("fa-eye-slash");
			}
		});
	}, 500); // Delay to ensure default icon is already rendered
});


document.addEventListener('DOMContentLoaded', function () {
  const initCouponFix = () => {
    const couponWrapper = document.querySelector('.wc-block-components-text-input.wc-block-components-totals-coupon__input');
    const couponInput = document.querySelector('#wc-block-components-totals-coupon__input-coupon');

    if (couponWrapper && couponInput) {
      setTimeout(() => {

        if (document.activeElement === couponInput) {
          couponInput.blur();
        }

        if (couponInput.value.trim() === '') {
          couponWrapper.classList.remove('is-active');
        }

        window.scrollTo({ top: 0, behavior: 'instant' });
      }, 100); 

      couponInput.addEventListener('focus', function () {
        couponWrapper.classList.add('is-active');
      });

      couponInput.addEventListener('blur', function () {
        if (couponInput.value.trim() === '') {
          couponWrapper.classList.remove('is-active');
        }
      });

      return true;
    }

    return false;
  };

  if (initCouponFix()) return;

  const observer = new MutationObserver(() => {
    if (initCouponFix()) {
      observer.disconnect();
    }
  });

  observer.observe(document.body, { childList: true, subtree: true });
});

