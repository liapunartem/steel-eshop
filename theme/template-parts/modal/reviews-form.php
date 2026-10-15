<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="modal" id="modal-reviews-form" role="dialog" aria-modal="true" aria-labelledby="modal-reviews-form-title" aria-hidden="true">
    <div class="modal__backdrop"></div>
    <div class="modal__body">
        <div class="modal__header">
            <span class="modal__title" id="modal-reviews-form-title"><?php esc_html_e( 'Залишити відгук', 'steel-eshop' ); ?></span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Закрити форму відгуку', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled" translate="no">close</span>
            </button>
        </div>
        
        

		<div class="reviews-form" id="review_form">
			<?php
			$commenter    = wp_get_current_commenter();
			$comment_form = array(
				'title_reply'         => '',
				'title_reply_to'      => '',
				'title_reply_before'  => '',
				'title_reply_after'   => '',
				'comment_notes_after' => '',
				'comment_notes_before' => '',
				'label_submit'        => esc_html__( 'Надіслати', 'steel-eshop' ),
				'logged_in_as'        => '',
				'comment_field'       => '',
				'submit_field'         => '%1$s %2$s',
				'class_submit'        => 'form__submit-button button',
				'class_form'           => 'comment-form form',
			);

			$name_email_required = (bool) get_option( 'require_name_email', 1 );
			$fields              = array(
				'author' => array(
					'label'        => __( "Ім'я", 'steel-eshop' ),
					'type'         => 'text',
					'value'        => $commenter['comment_author'],
					'required'     => $name_email_required,
					'autocomplete' => 'name',
				),
				'email'  => array(
					'label'        => __( 'E-mail', 'steel-eshop' ),
					'type'         => 'email',
					'value'        => $commenter['comment_author_email'],
					'required'     => $name_email_required,
					'autocomplete' => 'email',
				),
			);

			$comment_form['fields'] = array();

			foreach ( $fields as $key => $field ) {
				$field_html  = '<p class="form__field comment-form-' . esc_attr( $key ) . '">';
				$field_html .= '<label class="form__label" for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

				if ( $field['required'] ) {
					$field_html .= '&nbsp;<span class="required">*</span>';
				}

				$field_html .= '</label><input class="form__input" id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

				$comment_form['fields'][ $key ] = $field_html;
			}

			$account_page_url = wc_get_page_permalink( 'myaccount' );
			if ( $account_page_url ) {
				/* translators: %s opening and closing link tags respectively */
				$comment_form['must_log_in'] = '<p class="form__login must-log-in">' . sprintf( esc_html__( 'Щоб залишити відгук, вам потрібно %1$sувійти%2$s.', 'steel-eshop' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
			}

			if ( wc_review_ratings_enabled() ) {
				$comment_form['comment_field'] = '
					<div class="form__field reviews-form__rating comment-form-rating">
						<label class="form__label" for="rating" id="comment-form-rating-label">
							' . esc_html__( 'Ваша оцінка', 'steel-eshop' ) .
							( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '
						</label>

						<select class="form__select" name="rating" id="rating" required>
							<option value="">' . esc_html__( 'Оцінити&hellip;', 'steel-eshop' ) . '</option>
							<option value="5">' . esc_html__( 'Відмінно', 'steel-eshop' ) . '</option>
							<option value="4">' . esc_html__( 'Добре', 'steel-eshop' ) . '</option>
							<option value="3">' . esc_html__( 'Задовільно', 'steel-eshop' ) . '</option>
							<option value="2">' . esc_html__( 'Погано', 'steel-eshop' ) . '</option>
							<option value="1">' . esc_html__( 'Дуже погано', 'steel-eshop' ) . '</option>
						</select>
					</div>';
			}

			$comment_form['comment_field'] .= '
				<p class="form__field reviews-form__comment comment-form-comment">
					<label class="form__label" for="comment">
						' . esc_html__( 'Ваш відгук', 'steel-eshop' ) . '&nbsp;<span class="required">*</span>
					</label>

					<textarea class="form__textarea" id="comment" name="comment" cols="45" rows="8" required></textarea>
				</p>'
			;
			
			comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
			?>
		</div>



    </div>
</div>