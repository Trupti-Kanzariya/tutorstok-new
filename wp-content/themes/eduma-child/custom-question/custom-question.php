<?php
$current_user_id = get_current_user_id();
$per_page = 5;
$paged    = isset($_GET['paged']) ? (int) $_GET['paged'] : 1;
$offset   = ( $paged - 1 ) * $per_page;

$all_questions = get_posts([
	'post_type'      => 'lp_question',
	'author'         => $current_user_id,
    'posts_per_page' => -1,
    'post_status'    => 'publish',
]);

$total_questions = count( $all_questions );
$total_pages     = ceil( $total_questions / $per_page );
$questions       = array_slice( $all_questions, $offset, $per_page );
?>
<div id="flash-message" style="display:none;" class="custom-flash-msg"></div>

<div id="question-list-container" class="lp-instructor-question">
    <h3 class="profile-heading">My Questions</h3>
    <div class="custom-table-wrapper">
        <table class="lp-list-table instructor-assignment-list">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Author</th>
                    <th>Used In Tests</th>
                    <th>Actions</th>
                </tr>
            </thead>
          
            <tbody id="question-table-body">
                <?php foreach ($questions as $question): ?>
                    <tr>
                        <td><?php echo esc_html( $question->post_title ); ?></td>
                        <?php
                        $type_value  = get_post_meta( $question->ID, '_lp_type', true );
                        $type_labels = [
                            'true_or_false' => 'True/False',
                            'multi_choice'  => 'Multiple Choice',
                        ];
                        $type_label = isset( $type_labels[$type_value] ) ? $type_labels[$type_value] : ucfirst(str_replace( '_', ' ', $type_value ) );
                        ?>
                        <td><?php echo esc_html( $type_label ); ?></td>
                        <td><?php echo esc_html( get_the_author_meta( 'display_name', $question->post_author ) ); ?></td>
                        <td>
                            <?php
                            $used_in = [];
                            $quizzes = get_posts([
                                'post_type' => 'lp_quiz',
                                'posts_per_page' => -1,
                                'post_status' => 'publish',
                            ]);
                            foreach ($quizzes as $quiz) {
                                $questions_data = get_post_meta($quiz->ID, '_lp_questions', true);
                                if (!empty($questions_data['questions'])) {
                                    foreach ($questions_data['questions'] as $q) {
                                        if ($q['question_id'] == $question->ID) {
                                            $used_in[] = '<a href="' . get_permalink($quiz->ID) . '" target="_blank">' . esc_html($quiz->post_title) . '</a>';
                                        }
                                    }
                                }
                            }
                            echo !empty($used_in) ? implode(', ', $used_in) : '—';
                            ?>
                        </td>
                        <td>
                            <button class="edit-question-btn edit-assignment-btn bg-transparent-cls-adding-here-common" data-question-id="<?php echo $question->ID; ?>"></button>
                            <button class="delete-question-btn delete-assignment bg-transparent-cls-adding-here-common" data-question-id="<?php echo $question->ID; ?>"></button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5">
                       <div class="add-assignment-button">
                        <button id="add-new-question-btn" class="button-primary add-weekly-assignments new-weekly-assignment-cls-adding-here">Add New Question</button>
                    </div>
                    </td>
                </tr>
            </tfoot>
        </table>
        <div id="custom-confirm" class="custom-popup" style="display:none;">
    <div class="custom-popup-content">
        <p id="custom-confirm-message">Are you sure?</p>
        <button id="custom-confirm-yes">Yes</button>
        <button id="custom-confirm-no">No</button>
    </div>
</div>
    </div>
    <?php if ( $total_pages > 1 ) : ?>
    	<div class="lp-pagination custom-pagination">
    		<?php for ( $i = 1; $i <= $total_pages; $i++ ) :
    			$active_class = ( $i == $paged ) ? 'current' : '';
    			?>
    			<a href="?paged=<?php echo $i; ?>" class="pagination-link ajax-pagination-link <?php echo $active_class; ?>" data-page="<?php echo $i; ?>"><?php echo $i; ?></a>
    		<?php endfor; ?>
    	</div>
    <?php endif; ?>

</div>

<div id="question-form-container" style="display: none;">
    <div class="assignment-wrapper">
        <div class="assignment-title">
            <h2 id="form-title">Add Question</h2>
        </div>
        <div class="assignment-content">
            <form id="question-form">
                <input type="hidden" name="action" value="save_custom_question">
                <input type="hidden" name="question_id" id="question_id" value="">

                <div class="form-row">
                    <label>Title <span class="required">*</span></label>
                    <input type="text" name="question_title" required>
                </div>

                <div class="form-row">
                    <label>Content</label>
                    <textarea name="question_content" maxlength="255"></textarea>
                </div>

                <!-- <div class="form-row">
                    <label>Required</label>
                    <input type="checkbox" name="question_required" value="yes">
                </div>

                <div class="form-row">
                    <label>Shuffle Answers</label>
                    <input type="checkbox" name="shuffle_answers" value="yes">
                </div> -->

                

                <div class="bottom-form answer-option-cls-adding-here">
                    <label>Answer Options</label>
                    <div class="form-row answer-option-cls-adding-here-form-row">
                         <label>Type <span class="required">*</span></label>
                        <div class="select-type-all-options-here">
                        <select name="question_type" class="answer-option-cls-adding-here-form-question">
                            <option value="true_or_false">True/False</option>
                            <option value="multi_choice">Multiple Choice</option>
                        </select>
                    </div>
                    </div>
                    <div class="form-row">
                        <label>Answers</label>
                        <div class="input-content">
                            <div id="answer-options"></div>
                            <button type="button" id="add-answer" class="common-btn-remove-cls-adding-here">+ Add Answer</button>
                        </div>
                    </div>
                </div>

                <div class="bottom-form">
                    <h3>General Settings</h3>
                     <div class="form-row">
                        <label>Mark</label>
                        <div class="input-content">
                        <input type="number" name="question_mark" min="1" max="100">
                    </div>
                    </div>

                    <div class="form-row">
                        <label>Hint</label>
                        <div class="input-content">
                            <textarea name="question_hint" maxlength="500"></textarea>
                            <p>The instructions for the user to select the right answer. The text will be shown when users click the 'Hint' button.</p>
                        </div>
                    </div>

                    <div class="form-row">
                        <label>Explanation</label>
                        <div class="input-content">
                            <textarea name="question_explanation" maxlength="1000"></textarea>
                            <p>The explanation will be displayed when students click the "Check Answer" button.</p>
                        </div>
                    </div>
                </div>

                <br><br>
                <button type="submit" class="without-border-typed">Save</button>
                <button type="button" id="back-to-list" class="common-btn-remove-cls-adding-here">Back</button>
            </form>
        </div>
    </div>
</div>
<script>
    document.getElementById('question-list-container').addEventListener('click', function (e) {
    if (e.target.classList.contains('edit-question-btn')) {
        const questionId = e.target.dataset.questionId;
        fetch(ajaxurl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'load_custom_question',
                question_id: questionId
            })
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                const d = res.data;

                // Set form values
                document.getElementById('question_id').value = d.id;
                document.querySelector('[name="question_title"]').value = d.title;
                document.querySelector('[name="question_content"]').value = d.content;
                document.querySelector('[name="question_type"]').value = d.type;
                document.querySelector('[name="question_mark"]').value = d.mark;
                document.querySelector('[name="question_explanation"]').value = d.explanation;
                document.querySelector('[name="question_hint"]').value = d.hint;

                // Update form title and button text
                document.getElementById('form-title').textContent = 'Edit Question';
                document.querySelector('.without-border-typed').textContent = 'Update';

                // Populate answers
                answerContainer.innerHTML = '';
                d.answers.forEach(answer => {
                    const text = answer.value || answer.text || '';
                    addAnswerOption(text, answer.is_true === 'yes');
                });

                // Show/hide containers
                formContainer.style.display = 'block';
                listContainer.style.display = 'none';
            }
        });
    }
});

document.addEventListener('DOMContentLoaded', () => {
    const addNewBtn = document.getElementById('add-new-question-btn');
    const backBtn = document.getElementById('back-to-list');
    const formContainer = document.getElementById('question-form-container');
    const listContainer = document.getElementById('question-list-container');
    const answerContainer = document.getElementById('answer-options');

    const confirmBox = document.getElementById('custom-confirm');
    const confirmMsg = document.getElementById('custom-confirm-message');
    const confirmYes = document.getElementById('custom-confirm-yes');
    const confirmNo = document.getElementById('custom-confirm-no');

    // On page load, show any flash message stored in sessionStorage with toastr
    const flash = sessionStorage.getItem('flashMessage');
    if (flash) {
        toastr.success(flash);
        sessionStorage.removeItem('flashMessage');
    }

    // Confirm popup helper function
    function showConfirm(message, onConfirm) {
        confirmMsg.textContent = message;
        confirmBox.style.display = 'flex';

        const cleanup = () => {
            confirmBox.style.display = 'none';
            confirmYes.onclick = null;
            confirmNo.onclick = null;
        };

        confirmYes.onclick = () => {
            cleanup();
            onConfirm();
        };
        confirmNo.onclick = () => cleanup();
    }

    // Add answer option function (same as before)
    function addAnswerOption(text = '', isCorrect = false) {
        const row = document.createElement('div');
        row.className = 'answer-row answer-row-new-cls-adding-here';
        row.innerHTML = `
            <label><input type="checkbox" name="correct_answers[]" value="${answerContainer.children.length}" ${isCorrect ? 'checked' : ''}> Correct</label>
            <input type="text" name="answers[]" value="${text}">
            <button type="button" class="remove-answer delete-ics-cls-adding-here-close"></button>
        `;
        answerContainer.appendChild(row);
        row.querySelector('.remove-answer').addEventListener('click', () => row.remove());
    }

    // Add new answer button event
    document.getElementById('add-answer').addEventListener('click', () => addAnswerOption());

    addNewBtn.addEventListener('click', () => {
        document.getElementById('question-form').reset();
        answerContainer.innerHTML = '';
        document.getElementById('question_id').value = '';
        formContainer.style.display = 'block';
        listContainer.style.display = 'none';
    });

    backBtn.addEventListener('click', () => {
        formContainer.style.display = 'none';
        listContainer.style.display = 'block';
    });

    // Handle form submission
    document.getElementById('question-form').addEventListener('submit', function (e) {
        e.preventDefault();

        const saveButton = this.querySelector('button[type="submit"]');
        saveButton.disabled = true;

        const formData = new FormData(this);
        formData.append('action', 'save_custom_question');

        fetch(ajaxurl, {
            method: 'POST',
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            if (res.success) {
                sessionStorage.setItem('flashMessage', 'Question saved successfully!');
                location.reload(); // reload to update list
            } else {
                toastr.error("Error: " + res.data);
                saveButton.disabled = false;
            }
        })
        .catch(() => {
            toastr.error("Error submitting form.");
            saveButton.disabled = false;
        });
    });

    // Handle edit and delete buttons on the question list container
    document.getElementById('question-list-container').addEventListener('click', function (e) {
        if (e.target.classList.contains('edit-question-btn')) {
            const questionId = e.target.dataset.questionId;
            fetch(ajaxurl, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'load_custom_question',
                    question_id: questionId
                })
            })
            .then(res => res.json())
            .then(res => {
                if (res.success) {
                    const d = res.data;
                    document.getElementById('question_id').value = d.id;
                    document.querySelector('[name="question_title"]').value = d.title;
                    document.querySelector('[name="question_content"]').value = d.content;
                    document.querySelector('[name="question_type"]').value = d.type;
                    document.querySelector('[name="question_mark"]').value = d.mark;
                    document.querySelector('[name="question_explanation"]').value = d.explanation;
                    document.querySelector('[name="question_hint"]').value = d.hint;

                    answerContainer.innerHTML = '';
                    d.answers.forEach(answer => {
                        const text = answer.value || answer.text || '';
                        addAnswerOption(text, answer.is_true === 'yes');
                    });

                    formContainer.style.display = 'block';
                    listContainer.style.display = 'none';
                } else {
                    toastr.error("Error loading question.");
                }
            })
            .catch(() => toastr.error("Error loading question."));
        }

        if (e.target.classList.contains('delete-question-btn')) {
            const questionId = e.target.dataset.questionId;

            showConfirm("Are you sure you want to delete this question?", () => {
                fetch(ajaxurl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: new URLSearchParams({
                        action: 'delete_custom_question',
                        question_id: questionId
                    })
                })
                .then(res => res.json())
                .then(res => {
                    if (res.success) {
                        sessionStorage.setItem('flashMessage', 'Question deleted!');
                        location.reload();
                    } else {
                        toastr.error("Error: " + res.data);
                    }
                })
                .catch(() => toastr.error("Error deleting question."));
            });
        }
    });
});


</script>
<style type="text/css">
    .custom-popup {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
.custom-popup-content {
    background: #fff;
    padding: 20px 30px;
    border-radius: 8px;
    max-width: 400px;
    text-align: center;
}
.custom-popup-content p {
    margin-bottom: 20px;
}
.custom-popup-content button {
    padding: 8px 16px;
    margin: 0 5px;
    background: #0073aa;
    border: none;
    color: #fff;
    cursor: pointer;
}
.custom-popup-content button:hover {
    background: #005f8d;
}
.custom-flash-msg {
    background: #d4edda;
    color: #155724;
    padding: 15px;
    margin: 20px;
    border: 1px solid #c3e6cb;
    border-radius: 5px;
    text-align: center;
    font-weight: bold;
}
.required {
    color: red;
    margin-left: 2px;
}


</style>