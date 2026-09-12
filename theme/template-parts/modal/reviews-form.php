<?php
defined( 'ABSPATH' ) || exit;
?>

<div class="modal" id="modal-reviews-form" role="dialog" aria-modal="true" aria-labelledby="modal-reviews-form-title" aria-hidden="true">
    <div class="modal__backdrop"></div>
    <div class="modal__body">
        <div class="modal__header">
            <span class="modal__title" id="modal-reviews-form-title"><?php _e( 'Leave your review', 'steel-eshop' ); ?></span>
            <button type="button" class="modal__close-btn" aria-label="<?php esc_attr_e( 'Close review form', 'steel-eshop' ); ?>">
                <span class="material-symbols material-symbols--filled">close</span>
            </button>
        </div>
        
        

		<div class="reviews-form" id="review_form">
			<?php
			$commenter    = wp_get_current_commenter();
			$comment_form = array(
				/* translators: %s is product title */
				// 'title_reply'         => have_comments() ? esc_html__( 'Add a review', 'woocommerce' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
				'title_reply'        => '',
				/* translators: %s is product title */
				// 'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'woocommerce' ),
				'title_reply_to'      => '',
				// 'title_reply_before'  => '<span id="reply-title" class="form__title comment-reply-title" role="heading" aria-level="3">',
				'title_reply_before'  => '',
				// 'title_reply_after'   => '</span>',
				'title_reply_after'   => '',
				'comment_notes_after' => '',
				'comment_notes_before' => '',
				'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
				'logged_in_as'        => '',
				'comment_field'       => '',
				'submit_field'         => '%1$s %2$s',
				'class_submit'        => 'form__submit-button button',
				'class_form'           => 'comment-form form',
			);

			$name_email_required = (bool) get_option( 'require_name_email', 1 );
			$fields              = array(
				'author' => array(
					'label'        => __( 'Name', 'steel-eshop' ),
					'type'         => 'text',
					'value'        => $commenter['comment_author'],
					'required'     => $name_email_required,
					'autocomplete' => 'name',
				),
				'email'  => array(
					'label'        => __( 'Email', 'woocommerce' ),
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
				$comment_form['must_log_in'] = '<p class="form__login must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
			}

			if ( wc_review_ratings_enabled() ) {
				$comment_form['comment_field'] = '
					<div class="form__field reviews-form__rating comment-form-rating">
						<label class="form__label" for="rating" id="comment-form-rating-label">
							' . esc_html__( 'Your rating', 'woocommerce' ) .
							( wc_review_ratings_required() ? '&nbsp;<span class="required">*</span>' : '' ) . '
						</label>

						<select class="form__select" name="rating" id="rating" required>
							<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
							<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
							<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
							<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
							<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
							<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
						</select>
					</div>';
			}

			$comment_form['comment_field'] .= '
				<p class="form__field reviews-form__comment comment-form-comment">
					<label class="form__label" for="comment">
						' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required">*</span>
					</label>

					<textarea class="form__textarea" id="comment" name="comment" cols="45" rows="8" required></textarea>
				</p>'
			;
			
			comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
			?>
		</div>



    </div>
</div>