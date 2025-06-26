<?php
$current_user_id = get_current_user_id();
$per_page = 5; 
$paged    = isset($_GET['paged']) ? (int) $_GET['paged'] : 1;
$offset   = ($paged - 1) * $per_page;

// Get all quizzes
$all_quizzes = get_posts([
    'post_type'      => 'lp_quiz',
    'author'         => $current_user_id,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);

$total_quizzes = count($all_quizzes);
$total_pages   = ceil($total_quizzes / $per_page);
$quizzes = array_slice($all_quizzes, $offset, $per_page);

?>
<div id="quiz-message" style="display:none; padding:10px; margin-bottom:10px; border-radius:5px;"></div>

<div id="quiz-list-container" class="lp-instructor-quiz">
    <h2 class="profile-heading">My Quizzes</h2>
    <div class="custom-table-wrapper" id="quiz-table-wrapper">
        <table class="lp-list-table instructor-assignment-list">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Duration</th>
                    <th>Questions</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody id="quiz-list">
          <?php foreach ($quizzes as $quiz): 
            $duration_value = intval(get_post_meta($quiz->ID, '_lp_duration', true));
            $duration_unit_raw = get_post_meta($quiz->ID, 'quiz_duration_type', true);
            $duration_unit = $duration_unit_raw ? ucfirst($duration_unit_raw) . '(s)' : 'Minute(s)';

            $duration_display = $duration_value > 0 ? "$duration_value $duration_unit" : 'No limit';

            $questions = get_post_meta($quiz->ID, '_lp_questions', true);
            $question_count = is_array($questions) ? count($questions) : 0;
        ?>
            <tr>
                <td><?= esc_html($quiz->post_title); ?></td>
                <td><?= esc_html($duration_display); ?></td>
                <td><?= esc_html($question_count); ?></td>
                <td class="removebg-for-allics-cls-adding-here">
                    <button class="edit-quiz-btn edit-assignment-btn common-for-allics-cls-adding-here" data-quiz-id="<?= $quiz->ID ?>"></button>
                    <button class="delete-quiz-btn delete-assignment common-for-allics-cls-adding-here" data-quiz-id="<?= $quiz->ID ?>"></button>
                    <button class="duplicate-quiz-btn duplicate-submission-cls-adding common-for-allics-cls-adding-here" data-quiz-id="<?= $quiz->ID ?>"></button>
                </td>
            </tr>
        <?php endforeach; ?>

            </tbody>
            <div id="confirm-delete-modal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.6); z-index:9999; align-items:center; justify-content:center;">
              <div style="background:#fff; padding:20px; border-radius:8px; max-width:320px; text-align:center;">
                <p>Are you sure you want to delete this quiz?</p>
                <button id="confirm-delete-yes" style="margin-right:10px;">Yes</button>
                <button id="confirm-delete-no">No</button>
              </div>
            </div>

            <tfoot>
                <tr>
                    <td colspan="4">
                        <div class="add-assignment-button" style="margin-top: 20px;">
                           <button id="add-new-quiz-btn" class="new-weekly-assignment-cls-adding-here">Add New Quiz</button>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
      <?php if ($total_pages > 1): ?>
        <div class="lp-pagination custom-pagination">
           <?php for ( $i = 1; $i <= $total_pages; $i++ ) :
                $active_class = ( $i == $paged ) ? 'current' : '';
                ?>
                <a href="?paged=<?php echo $i; ?>" class="pagination-link quiz-pagination-link <?php echo $active_class; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    <?php endif; ?>
    
</div>

<div id="quiz-form-container" style="display: none;">
    <div class="assignment-wrapper">
        <h3 id="quiz-form-title" class="profile-heading">Add Quiz</h3>
        <div class="assignment-content">
            <form id="quiz-form">
                <input type="hidden" name="action" value="save_custom_quiz">
                <input type="hidden" name="quiz_id" id="quiz_id" value="">

                <div class="form-row">
                   <label>Title <span class="required">*</span></label>
                    <input type="text" name="quiz_title" required maxlength="100">
                </div>

                <div class="form-row">
                    <label>Content</label>
                    <textarea name="quiz_content" maxlength="1000"></textarea>
                </div>

                <div class="bottom-form">
                <div class="form-row">
                    <label>Link Questions <span class="required">*</span></label>
                    <div class="input-content">
                        <select name="linked_questions[]" multiple class="link-questions-cls-adding-here" required>
                            <?php
                            $questions = get_posts(['post_type' => 'lp_question', 'author' => $current_user_id, 'posts_per_page' => -1]);
                            foreach ($questions as $q) {
                                echo '<option value="' . esc_attr($q->ID) . '">' . esc_html($q->post_title) . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

                <div class="bottom-form">
                    <h3>Settings</h3>
                    <div class="form-row">
                        <label>Duration <span class="required">*</span></label>
                        <div class="flexing-all-typed-here">
                        <div class="input-content">
                        <input type="number" name="quiz_duration" min="0" max="200" step="1" required>
                        <div class="limit-set-cls-adding-here">
                             <span class="description">Set to 0 for no limit.</span>
                        </div>
                        </div>
                        <div class="select-typed-here select-type-all-options-here">
                        <select name="quiz_duration_1" class="answer-option-cls-adding-here-form-question">
                            <option value="minute">Minute(s)</option>
                            <option value="hour">Hour(s)</option>
                            <option value="day">Day(s)</option>
                            <option value="week">Week(s)</option>
                        </select>
                        </div>
                        </div>
                       
                    </div>

                    <div class="bottom-form">
                    <div class="form-row">
                        <label>Passing Grade (%)</label>
                        <div class="input-content">
                        <input type="number" name="quiz_passing_grade" min="0" max="100">
                    </div>
                    </div>
                </div>
                </div>

                <br><br>
                <button type="submit" class="without-border-typed">Save</button>
                <button type="button" id="quiz-back-btn" class="common-btn-remove-cls-adding-here">Back</button>
            </form>
        </div>
    </div>
</div>
<style type="text/css">
    .required {
    color: red;
    margin-left: 4px;
}

</style>
<script>

document.addEventListener('DOMContentLoaded', () => {

document.getElementById('add-new-quiz-btn').addEventListener('click', () => {
  quizForm.reset();
  quizForm.quiz_id.value = '';
  formTitle.textContent = 'Add Quiz'; 
  submitButton.textContent = 'Save';  
  submitButton.disabled = false;

  formContainer.style.display = 'block';
  quizContainer.style.display = 'none';
});
  const quizForm = document.getElementById('quiz-form');
  const quizContainer = document.getElementById('quiz-list-container');
  const formContainer = document.getElementById('quiz-form-container');
  const quizList = document.getElementById('quiz-list');
  const messageBox = document.getElementById('quiz-message');
  const modal = document.getElementById('confirm-delete-modal');
  const modalYes = document.getElementById('confirm-delete-yes');
  const modalNo = document.getElementById('confirm-delete-no');
  const formTitle = document.getElementById('quiz-form-title');
  const submitButton = quizForm.querySelector('button[type="submit"]');
  let quizIdToDelete = null;
  // Show message helper
  function showMessage(text, type = 'success') {
    if (typeof toastr !== 'undefined') {
      if (type === 'error') {
        toastr.error(text);
      } else {
        toastr.success(text);
      }
    } else {
      // fallback to your current message box
      if (!messageBox) return;
      messageBox.style.display = 'block';
      messageBox.textContent = text;

      if (type === 'error') {
        messageBox.style.backgroundColor = '#f8d7da';
        messageBox.style.color = '#721c24';
        messageBox.style.border = '1px solid #f5c6cb';
      } else {
        messageBox.style.backgroundColor = '#d4edda';
        messageBox.style.color = '#155724';
        messageBox.style.color = '#155724';
        messageBox.style.border = '1px solid #c3e6cb';
      }

      setTimeout(() => {
        messageBox.style.display = 'none';
      }, 4000);
    }
  }

  if (!quizList) {
    console.error('Element #quiz-list not found!');
    return;
  }

  // Event delegation for quiz list
  quizList.addEventListener('click', function (e) {
    const target = e.target;

    // Edit quiz
    if (target.classList.contains('edit-quiz-btn')) {
      const id = target.dataset.quizId;

      fetch(ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'load_custom_quiz', quiz_id: id })
      })
      .then(res => res.json())
      .then(res => {
        if (res.success) {
          const d = res.data;
          quizForm.quiz_id.value = d.id;
          quizForm.quiz_title.value = d.title;
          quizForm.quiz_content.value = d.content;
          quizForm.quiz_duration.value = d.quiz_duration;
          quizForm.quiz_duration_1.value = d.quiz_duration_type || 'minute';
          quizForm.quiz_passing_grade.value = d.passing_grade;

          const selects = quizForm['linked_questions[]'].options;
          for (let i = 0; i < selects.length; i++) {
            selects[i].selected = d.questions.includes(selects[i].value);
          }
          formTitle.textContent = 'Edit Quiz'; 
          submitButton.textContent = 'Update'; 
          submitButton.disabled = false;
          formContainer.style.display = 'block';
          quizContainer.style.display = 'none';
        } else {
          showMessage('Failed to load quiz', 'error');
        }
      })
      .catch(() => showMessage('Error loading quiz', 'error'));
    }

    // Delete quiz (open modal)
    if (target.classList.contains('delete-quiz-btn')) {
      const id = target.dataset.quizId;
      if (!id) {
        showMessage('Quiz ID missing!', 'error');
        return;
      }
      quizIdToDelete = id;
      modal.style.display = 'flex';
    }

    // Duplicate quiz
    if (target.classList.contains('duplicate-quiz-btn')) {
      const id = target.dataset.quizId;

      fetch(ajaxurl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ action: 'duplicate_custom_quiz', quiz_id: id })
      })
      .then(res => {
        if (res.ok) {
          showMessage('Quiz duplicated successfully!');
          setTimeout(() => location.reload(), 1000);
        } else {
          showMessage('Error duplicating quiz', 'error');
        }
      })
      .catch(() => showMessage('Error duplicating quiz', 'error'));
    }
  });

  // Handle modal confirmation for delete
  modalYes.addEventListener('click', () => {
    if (!quizIdToDelete) return;

    fetch(ajaxurl, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams({ action: 'delete_custom_quiz', quiz_id: quizIdToDelete })
    })
    .then(res => {
      if (res.ok) {
        showMessage('Quiz deleted successfully!');
        setTimeout(() => location.reload(), 1000);
      } else {
        showMessage('Failed to delete quiz', 'error');
      }
    })
    .catch(() => showMessage('Error deleting quiz', 'error'));

    modal.style.display = 'none';
    quizIdToDelete = null;
  });

  modalNo.addEventListener('click', () => {
    modal.style.display = 'none';
    quizIdToDelete = null;
  });

  // Show new form
  document.getElementById('add-new-quiz-btn').addEventListener('click', () => {
    quizForm.reset();
    quizForm.quiz_id.value = '';
    formContainer.style.display = 'block';
    quizContainer.style.display = 'none';
  });

  // Back to list
  document.getElementById('quiz-back-btn').addEventListener('click', () => {
    formContainer.style.display = 'none';
    quizContainer.style.display = 'block';
  });

  // Form submit
  quizForm.addEventListener('submit', e => {
    e.preventDefault();

    // Get the submit button inside the form
    const submitButton = quizForm.querySelector('button[type="submit"]');
    submitButton.disabled = true;           
    submitButton.textContent = 'Saving...';  

    const formData = new FormData(quizForm);

    fetch(ajaxurl, {
      method: 'POST',
      body: formData
    })
    .then(res => res.json())
    .then(res => {
      if (res.success) {
        const isEdit = quizForm.quiz_id.value.trim() !== '';
        const message = isEdit ? 'Quiz updated successfully!' : 'Quiz created successfully!';
        showMessage(message);
        setTimeout(() => location.reload(), 1000);
        } else {
          showMessage('Error: ' + (res.data || 'Unknown error'), 'error');
          submitButton.disabled = false;         
          submitButton.textContent = 'Save';      
        }
      })
    .catch(() => {
      showMessage('Error saving quiz', 'error');
      submitButton.disabled = false;            
      submitButton.textContent = 'Save';        
    });
  });
});

</script>
