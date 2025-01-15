<?php
wp_nonce_field( 'cmm_donation_meta_box', 'cmm_donation_meta_box_nonce' );
?>
<section class="tabs-wrapper">
	<div class="tabs-container">
		<div class="tabs-block">
			<div id="tabs-section" class="tabs">
				<ul class="tab-head">
					<li>
						<a href="#tab-1" class="tab-link active">One-off Donation</a>
					</li>
					<li>
						<a href="#tab-2" class="tab-link">Recurring Donation</a>
					</li>								
				</ul>

				<section id="tab-1" class="tab-body entry-content active active-content">
					<div class="cmm_donation_content_heading">
						<h2>One-off Donation Setting</h2>
					</div>
					<div class="cmm_donation_form_wrap">
						<?php
						global $post;
						$single_title = get_post_meta( $post->ID, '_cmm_donation_single_title', true );
						$single_sub_title = get_post_meta( $post->ID, '_cmm_donation_single_sub_title', true );
						$single_heading = get_post_meta( $post->ID, '_cmm_donation_single_heading', true );
						$single_other_amt = get_post_meta( $post->ID, '_cmm_donation_single_other_amt', true );
						$single_other_amt_text = get_post_meta( $post->ID, '_cmm_donation_single_other_amt_text', true );
						$single_other_amt_desc = get_post_meta( $post->ID, '_cmm_donation_single_other_amt_desc', true );

						$single_button = get_post_meta( $post->ID, '_cmm_donation_single_button', true );
						$single_layout = get_post_meta( $post->ID, '_cmm_donation_single_layout', true );
						$single_amount = get_post_meta( $post->ID, '_cmm_donation_single_amount', true );
						$layout = '';
						if( $single_layout ){
							$layout = 'layout-'. $single_layout;
						}
						?>
						<div class="form-inner">
							<?php						
							$selected_list = '';
							$selected_grid = '';

							if( $single_layout == 'list'){
								$selected_list = 'selected';
							} elseif( $single_layout == 'grid'){
								$selected_grid = 'selected';
							} 
							?>
							<label>Donation Layout</label>
							<select class="" name="cmm-donation-single-layout" id="cmm-donation-single-layout">								
								<option value="grid" <?php echo $selected_grid;?> >Grid</option>
								<option value="list" <?php echo $selected_list;?>>List</option>
							</select>				
						</div>
						<div class="form-inner">				
							<label>Donation title</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-single-title" id="cmm-donation-single-title" value="<?php echo esc_attr($single_title); ?>" />				
						</div>
						<div class="form-inner">				
							<label>Donation Sub-title</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-single-subtitle" id="cmm-donation-single-subtitle" value="<?php echo esc_attr($single_sub_title); ?>" />				
						</div>
						<div class="form-inner">				
							<label>Donation Heading</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-single-heading" id="cmm-donation-single-heading" value="<?php echo esc_attr($single_heading); ?>" />				
						</div>
						<div class="form-inner">				
							<label>Show Other Amount</label>
							<?php
							$checked = '';
							$show_text = 'display:none;';
							if( $single_other_amt == 1 ){
								$checked = 'checked="checked"';
								$show_text = '';
							}
							?>
							<input class="" type="checkbox" name="cmm-donation-single-other-amt" id="cmm-donation-single-other-amt" value="1" <?php echo $checked; ?> />				
						</div>
						<div class="form-inner single-other-amount-text" style="<?php echo $show_text;?>">				
							<label>Other Amount Label</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-single-other-amt-txt" id="cmm-donation-single-other-amt-txt" value="<?php echo esc_attr($single_other_amt_text); ?>" />				
						</div>
						<div class="form-inner single-other-amount-desc <?php echo $layout;?>" style="<?php echo $show_text;?>">				
							<label>Other Amount Description</label>
							<textarea class="input-text regular-input" type="text" name="cmm-donation-single-other-amt-desc"><?php echo esc_attr($single_other_amt_desc); ?></textarea>			
						</div>
						<div class="form-inner">				
							<label>Button Text</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-single-btn-txt" id="cmm-donation-single-btn-txt" value="<?php echo esc_attr($single_button); ?>" />				
						</div>
						
						<div class="single-donation-amount-wrap <?php echo esc_attr($layout); ?>">
							<div class="cmm_donation_content_heading">
								<h2>Add Donation Amount</h2>
							</div>
							
							<?php
							if( $single_amount ){
								$cnt = 1;
								$total = count($single_amount);
								foreach($single_amount as $k=>$data){
								?>
								<div class="donation-amount-inner" data-index="<?php echo $k;?>">
									<div class="form-inner donation-amount">				
										<label>Amount</label>
										<input class="input-text regular-input" type="text" name="cmm-donation-single-amt[<?php echo $k;?>][amount]" value="<?php echo $data['amount'];?>" />				
									</div>
									<div class="form-inner donation-label">				
										<label>Label</label>
										<input class="input-text regular-input" type="text" name="cmm-donation-single-amt[<?php echo $k;?>][label]" value="<?php echo $data['label'];?>" />				
									</div>
									<div class="form-inner donation-desc">				
										<label>Description</label>
										<textarea class="input-text regular-input" type="text" name="cmm-donation-single-amt[<?php echo $k;?>][desc]"><?php echo $data['desc'];?></textarea>				
									</div>
									<?php
									if( $total == 1 ){
										$show_add = '';
										$show_del = 'display:none;';									
									}elseif( $cnt == $total && $total > 1 ){
										$show_add = '';
										$show_del = '';									
									} else{
										$show_add = 'display:none;';
										$show_del = '';									
									}
									?>
									<a href="" class="amount-add" style="<?php echo $show_add; ?>">Add New</a>
									<a href="" class="amount-delete" style="<?php echo $show_del; ?>">Delete</a>
								
								</div>
								<?php
								$cnt++;
								}
							?>
							<?php	
							} else{
    						?>
							<div class="donation-amount-inner" data-index="0">
								<div class="form-inner donation-amount">				
									<label>Amount</label>
									<input class="input-text regular-input" type="text" name="cmm-donation-single-amt[0][amount]" value="" />				
								</div>
								<div class="form-inner donation-label">				
									<label>Label</label>
									<input class="input-text regular-input" type="text" name="cmm-donation-single-amt[0][label]" value="" />				
								</div>
								<div class="form-inner donation-desc">				
									<label>Description</label>
									<textarea class="input-text regular-input" type="text" name="cmm-donation-single-amt[0][desc]"></textarea>				
								</div>
								
									<a href="" class="amount-add">Add New</a>
									<a href="" class="amount-delete" style="display:none;">Delete</a>
								
							</div>
							<?php
							}
							?>
						</div>						
						
					</div>
				</section>

				<section id="tab-2" class="tab-body entry-content">
					<div class="cmm_donation_content_heading">
						<h2>Recurring Donation Setting</h2>
					</div>
					<div class="cmm_donation_form_wrap">
						<?php
						global $post;
						$recurring_title = get_post_meta( $post->ID, '_cmm_donation_recurring_title', true );
						$recurring_sub_title = get_post_meta( $post->ID, '_cmm_donation_recurring_sub_title', true );
						$recurring_heading = get_post_meta( $post->ID, '_cmm_donation_recurring_heading', true );
						$recurring_other_amt = get_post_meta( $post->ID, '_cmm_donation_recurring_other_amt', true );
						$recurring_other_amt_text = get_post_meta( $post->ID, '_cmm_donation_recurring_other_amt_text', true );
						$recurring_other_amt_desc = get_post_meta( $post->ID, '_cmm_donation_recurring_other_amt_desc', true );
						$recurring_frequency = get_post_meta( $post->ID, '_cmm_donation_recurring_frequency', true );
						$recurring_button = get_post_meta( $post->ID, '_cmm_donation_recurring_button', true );
						$recurring_layout = get_post_meta( $post->ID, '_cmm_donation_recurring_layout', true );
						$recurring_amount = get_post_meta( $post->ID, '_cmm_donation_recurring_amount', true );
						$layout = '';
						if( $recurring_layout ){
							$layout = 'layout-'. $recurring_layout;
						}
						?>
						<div class="form-inner">
							<?php						
							$selected_list = '';
							$selected_grid = '';
							
							if( $recurring_layout == 'list'){
								$selected_list = 'selected';
							} elseif( $recurring_layout == 'grid'){
								$selected_grid = 'selected';
							}
							?>
							<label>Donation Layout</label>
							<select class="" name="cmm-donation-recurring-layout" id="cmm-donation-recurring-layout">
								<option value="grid" <?php echo $selected_grid;?> >Grid</option>
								<option value="list" <?php echo $selected_list;?>>List</option>
							</select>				
						</div>
						<div class="form-inner">				
							<label>Donation title</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-recurring-title" id="cmm-donation-recurring-title" value="<?php echo esc_attr($recurring_title); ?>" />				
						</div>
						<div class="form-inner">				
							<label>Donation Sub-title</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-recurring-subtitle" id="cmm-donation-recurring-subtitle" value="<?php echo esc_attr($recurring_sub_title); ?>" />				
						</div>
						<div class="form-inner">				
							<label>Donation Heading</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-recurring-heading" id="cmm-donation-recurring-heading" value="<?php echo esc_attr($recurring_heading); ?>" />				
						</div>
						<div class="form-inner">				
							<label>Show Other Amount</label>
							<?php
							$checked = '';
							$show_text = 'display:none;';
							if( $recurring_other_amt == 1 ){
								$checked = 'checked="checked"';
								$show_text = '';
							}
							?>
							<input class="" type="checkbox" name="cmm-donation-recurring-other-amt" id="cmm-donation-recurring-other-amt" value="1" <?php echo $checked; ?> />				
						</div>
						<div class="form-inner recurring-other-amount-text" style="<?php echo $show_text;?>">				
							<label>Other Amount Text</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-recurring-other-amt-txt" id="cmm-donation-recurring-other-amt-txt" value="<?php echo esc_attr($recurring_other_amt_text); ?>" />				
						</div>	
						<div class="form-inner recurring-other-amount-desc <?php echo $layout;?>" style="<?php echo $show_text;?>">				
							<label>Other Amount Description</label>
							<textarea class="input-text regular-input" type="text" name="cmm-donation-recurring-other-amt-desc"><?php echo esc_attr($recurring_other_amt_desc); ?></textarea>			
						</div>					
						<div class="form-inner">
							<?php						
							$selected_weekly = '';
							$selected_fortnightly = '';
							$selected_monthly = '';
							$selected_quarterly = '';
							$selected_half_year = '';
							$selected_annually = '';

							if( $recurring_frequency == 'WEEKLY'){
								$selected_weekly = 'selected';
							} elseif( $recurring_frequency == 'FORTNIGHTLY'){
								$selected_fortnightly = 'selected';
							} elseif( $recurring_frequency == 'MONTHLY'){
								$selected_monthly = 'selected';
							} elseif( $recurring_frequency == 'QUARTERLY'){
								$selected_quarterly = 'selected';
							} elseif( $recurring_frequency == 'HALF_YEARLY'){
								$selected_half_year = 'selected';
							} elseif( $recurring_frequency == 'ANNUALLY'){
								$selected_annually = 'selected';
							}
							?>
							<label>Donation Frequency</label>
							<select class="" name="cmm-donation-recurring-frequency" id="cmm-donation-recurring-frequency">
								<option>-- Select Frequency --</option>
								<option value="WEEKLY" <?php echo $selected_weekly;?>>Weekly</option>
								<option value="FORTNIGHTLY" <?php echo $selected_fortnightly;?> >Fortnightly</option>
								<option value="MONTHLY"<?php echo $selected_monthly;?> >Monthly</option>
								<option value="QUARTERLY"<?php echo $selected_quarterly;?> >Quarterly</option>
								<option value="HALF_YEARLY"<?php echo $selected_half_year;?> >Half Yearly</option>
								<option value="ANNUALLY"<?php echo $selected_annually;?> >Annually</option>
							</select>				
						</div>						
						
						
						<div class="form-inner">				
							<label>Button Text</label>
							<input class="input-text regular-input" type="text" name="cmm-donation-recurring-btn-txt" id="cmm-donation-recurring-btn-txt" value="<?php echo esc_attr($recurring_button); ?>" />				
						</div>
						<div class="recurring-donation-amount-wrap <?php echo esc_attr($layout); ?>">
							<div class="cmm_donation_content_heading">
								<h2>Add Donation Amount</h2>
							</div>
							
							<?php
							if( $recurring_amount ){
								$cnt = 1;
								$total = count($recurring_amount);
								foreach($recurring_amount as $k=>$data){
								?>
								<div class="donation-amount-inner" data-index="<?php echo $k;?>">
									<div class="form-inner donation-amount">				
										<label>Amount</label>
										<input class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[<?php echo $k;?>][amount]" value="<?php echo $data['amount'];?>" />				
									</div>
									<div class="form-inner donation-label">				
										<label>Label</label>
										<input class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[<?php echo $k;?>][label]" value="<?php echo $data['label'];?>" />				
									</div>
									<div class="form-inner donation-desc">				
										<label>Description</label>
										<textarea class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[<?php echo $k;?>][desc]"><?php echo $data['desc'];?></textarea>				
									</div>
									<?php
									if( $total == 1 ){
										$show_add = '';
										$show_del = 'display:none;';									
									}elseif( $cnt == $total && $total > 1 ){
										$show_add = '';
										$show_del = '';									
									} else{
										$show_add = 'display:none;';
										$show_del = '';									
									}
									?>
									<a href="" class="amount-add" style="<?php echo $show_add; ?>">Add New</a>
									<a href="" class="amount-delete" style="<?php echo $show_del; ?>">Delete</a>
								
								</div>
								<?php
								$cnt++;
								}
							?>
							<?php	
							} else{
    						?>
							<div class="donation-amount-inner" data-index="0">
								<div class="form-inner donation-amount">				
									<label>Amount</label>
									<input class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[0][amount]" value="" />				
								</div>
								<div class="form-inner donation-label">				
									<label>Label</label>
									<input class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[0][label]" value="" />				
								</div>
								<div class="form-inner donation-desc">				
									<label>Description</label>
									<textarea class="input-text regular-input" type="text" name="cmm-donation-recurring-amt[0][desc]"></textarea>				
								</div>
								
									<a href="" class="amount-add">Add New</a>
									<a href="" class="amount-delete" style="display:none;">Delete</a>
								
							</div>
							<?php
							}
							?>
						</div>							
						
					</div>
				</section>

			</div>
		</div>
	</div>
</section>
