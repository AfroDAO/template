<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Facebook Settings
 *
 * The html markup for the Facebook settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_fb_posting;

// model class
$model = $wpw_auto_poster_model;

// facebook posting class
$fbposting = $wpw_auto_poster_fb_posting;

// get all post methods
$wall_post_methods = $model->wpw_auto_poster_get_fb_posting_method();

$facebook_keys = isset( $wpw_auto_poster_options['facebook_keys'] ) ? $wpw_auto_poster_options['facebook_keys'] : array();

$wpw_auto_poster_fb_sess_data = get_option( 'wpw_auto_poster_fb_sess_data' ); // Getting facebook app grant data

$fb_app_version = ( !empty( $wpw_auto_poster_options['fb_app_version'] ) ) ? $wpw_auto_poster_options['fb_app_version'] : '';

$fb_app_versions = array( '208' => '2.8 or below', '209' => '2.9 or above');

$fb_wp_pretty_url = ( !empty( $wpw_auto_poster_options['fb_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['fb_wp_pretty_url'] : '';

$fb_wp_pretty_url = !empty( $fb_wp_pretty_url ) ? ' checked="checked"' : '';
$fb_wp_pretty_url_css = ( $wpw_auto_poster_options['fb_url_shortener'] == 'wordpress' ) ? ' display:table-row': ' display:none';

$wpw_auto_poster_options['fb_global_message_template'] = ( isset( $wpw_auto_poster_options['fb_global_message_template'] ) ) ? $wpw_auto_poster_options['fb_global_message_template'] : '';

// get url shortner service list array 
$fb_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$fb_exclude_cats = array();

?>

<!-- beginning of the facebook general settings meta box -->
<div id="wpw-auto-poster-facebook-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="facebook_general" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'Facebook General Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table">											
							<tbody>				
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_facebook]"><?php _e( 'Enable Autoposting to Facebook:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input name="wpw_auto_poster_options[enable_facebook]" id="wpw_auto_poster_options[enable_facebook]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_facebook'] ) ) { checked( '1', $wpw_auto_poster_options['enable_facebook'] ); } ?> />
										<p><small><?php _e( 'Check this box, if you want to automatically post your new content to Facebook.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_facebook_for]"><?php _e( 'Enable Facebook Autoposting for:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<ul>
										<?php 
											$all_types = get_post_types( array( 'public' => true ), 'objects');
											$all_types = is_array( $all_types ) ? $all_types : array();
											
											if( !empty( $wpw_auto_poster_options['enable_facebook_for'] ) ) {
												$prevent_meta = $wpw_auto_poster_options['enable_facebook_for'];
											} else {
												$prevent_meta = array();
											}

											if( !empty( $wpw_auto_poster_options['fb_post_type_tags'] ) ) {
												$fb_post_type_tags = $wpw_auto_poster_options['fb_post_type_tags'];
											} else {
												$fb_post_type_tags = array();
											}

											$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

											if( !empty( $wpw_auto_poster_options['fb_post_type_cats'] ) ) {
												$fb_post_type_cats = $wpw_auto_poster_options['fb_post_type_cats'];
											} else {
												$fb_post_type_cats = array();
											}

											// Get saved categories for fb to exclude from posting
											if( !empty( $wpw_auto_poster_options['fb_exclude_cats'] ) ) {
												$fb_exclude_cats = $wpw_auto_poster_options['fb_exclude_cats'];
											}

											foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;															
													$label = @$type->labels->name ? $type->labels->name : $type->name;
													if( $label == 'Media' || $label == 'media' ) continue; // skip media
													$selected = ( in_array( $type->name, $prevent_meta ) ) ? 'checked="checked"' : '';
													
										?>
															
											<li class="wpw-auto-poster-prevent-types">
												<input type="checkbox" id="wpw_auto_posting_facebook_prevent_<?php echo $type->name; ?>" name="wpw_auto_poster_options[enable_facebook_for][]" value="<?php echo $type->name; ?>" <?php echo $selected; ?>/>
																						
												<label for="wpw_auto_posting_facebook_prevent_<?php echo $type->name; ?>"><?php echo $label; ?></label>
											</li>
											
											<?php	} ?>
										</ul>
										<p><small><?php _e( 'Check each of the post types that you want to post automatically to Facebook when they get published.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_post_type_tags][]"><?php _e( 'Select Tag:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[fb_post_type_tags][]" id="wpw_auto_poster_options[fb_post_type_tags]" class="fb_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
											<?php foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;	

													if(in_array( $type->name, $prevent_meta )) {

														$label = @$type->labels->name ? $type->labels->name : $type->name;
														if( $label == 'Media' || $label == 'media' ) continue; // skip media
														$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );
	                							
	                									echo '<optgroup label="'.$label.'">';
										                // Loop on all taxonomies
										                foreach ($all_taxonomies as $taxonomy){

										                	$selected = '';
										                	if( !empty( $static_post_type_arr[$type->name] ) && $static_post_type_arr[$type->name] != $taxonomy->name){
                             										continue;
                    										}
										                	if(isset($fb_post_type_tags[$type->name]) && !empty($fb_post_type_tags[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $fb_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

										                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader">
  											<img src="<?php echo WPW_AUTO_POSTER_IMG_URL."/icons/ajax-loader.gif";?>"/>
										</div>
										<p><small><?php _e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php _e('hashtags.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_post_type_cats][]"><?php _e( 'Select Categories:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[fb_post_type_cats][]" id="wpw_auto_poster_options[fb_post_type_cats]" class="fb_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
											<?php foreach( $all_types as $type ) {	
												
												if( !is_object( $type ) ) continue;	

													if(in_array( $type->name, $prevent_meta )) {														
														$label = @$type->labels->name ? $type->labels->name : $type->name;
														if( $label == 'Media' || $label == 'media' ) continue; // skip media
														$all_taxonomies = get_object_taxonomies( $type->name, 'objects' );
	                							
	                									echo '<optgroup label="'.$label.'">';
										                // Loop on all taxonomies
										                foreach ($all_taxonomies as $taxonomy){

										                	$selected = '';
										                	if(isset($fb_post_type_cats[$type->name]) && !empty($fb_post_type_cats[$type->name])){
										                		$selected = ( in_array( $taxonomy->name, $fb_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

										                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader">
  											<img src="<?php echo WPW_AUTO_POSTER_IMG_URL."/icons/ajax-loader.gif";?>"/>
										</div>
										<p><small><?php _e( 'Select the Categories for each post type that you want to post categories as ', 'wpwautoposter' ); ?><b><?php _e('hashtags.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_exclude_cats][]"><?php _e( 'Exclude Specific Categories:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[fb_exclude_cats][]" id="wpw_auto_poster_options[fb_exclude_cats]" class="fb_exclude_cats wpw-auto-poster-cats-exclude-select" multiple="multiple">
											
											<?php

												$post_type_categories = wpw_auto_poster_get_all_categories();

												if(!empty($post_type_categories)) {
													
													foreach($post_type_categories as $post_type => $post_data){

														echo '<optgroup label="'.$post_data['label'].'">';

														if(isset($post_data['categories']) && !empty($post_data['categories']) && is_array($post_data['categories'])){
															
															foreach($post_data['categories'] as $cat_slug => $cat_name){

																$selected ='';
																if( !empty( $fb_exclude_cats[$post_type] ) ) {
											                		$selected = ( in_array( $cat_slug, $fb_exclude_cats[$post_type] ) ) ? 'selected="selected"' : '';
											                	}
											                	
																echo '<option value="' . $post_type ."|".$cat_slug . '" '.$selected.'>'.$cat_name.'</option>';
															}

														}
														echo '</optgroup>';
													}
												}

											?>

										</select>
										<p><small><?php _e( 'Select the categories for each post type that you want to exclude for posting.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>	
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_app_version]"><?php _e( 'Facebook App Version:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[fb_app_version]" id="wpw_auto_poster_options[fb_app_version]" class="fb_app_version">
											<?php foreach ( $fb_app_versions as $key => $version ) {?>
												<option value="<?php print $key;?>" <?php selected( $fb_app_version, $key ); ?>><?php print $version;?></option>
											<?php } ?>
										</select>
										<p><small><?php _e( 'Select Facebook App version you are using for auto posting. Please make sure you create all Facebook apps with version "2.8 or below" OR you create all Facebook apps with version "2.9 or above".', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
															
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_url_shortener]"><?php _e( 'URL Shortener:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[fb_url_shortener]" id="wpw_auto_poster_options[fb_url_shortener]" class="fb_url_shortener" data-content='fb'>
											<?php
																
												foreach ( $fb_url_shortener as $key => $option ) {											
													?>
													<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $wpw_auto_poster_options['fb_url_shortener'], $key ); ?>>
														<?php esc_html_e( $option ); ?>
													</option>
													<?php
												}
											?>
										</select>
										<p><small><?php _e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>

								<tr id="row-fb-wp-pretty-url" valign="top" style="<?php print $fb_wp_pretty_url_css;?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_wp_pretty_url]"><?php _e( 'Pretty permalink URL:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<input type="checkbox" name="wpw_auto_poster_options[fb_wp_pretty_url]" id="wpw_auto_poster_options[fb_wp_pretty_url]" class="fb_wp_pretty_url" data-content='fb' value="yes" <?php print $fb_wp_pretty_url;?>>
										<p><small><?php _e( 'Check this box if you want to use pretty permalink. i.e. http://example.com/test-post/. (Not Recommnended).', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
								
								<?php	        
									if( $wpw_auto_poster_options['fb_url_shortener'] == 'bitly' ) {	        		
										$class = '';	        		
									} else {	        		
										$class = ' style="display:none;"';
									}
									
									if( $wpw_auto_poster_options['fb_url_shortener'] == 'shorte.st' ) {
										$shortest_class = '';	        		
									} else {	        		
										$shortest_class = ' style="display:none;"';
									}
									
								  	if ($wpw_auto_poster_options['fb_url_shortener'] == 'google_shortner') {
		                                $google_shortner_cls = '';
		                            } else {
		                                $google_shortner_cls = ' style="display:none;"';
		                            }
								?>
								
								<tr valign="top" class="fb_setting_input_bitly"<?php echo $class; ?>>
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_bitly_access_token]"><?php _e( 'Bit.ly Access Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[fb_bitly_access_token]" id="wpw_auto_poster_options[fb_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['fb_bitly_access_token'] ); ?>" class="large-text">
									</td>
								</tr>
								
								<tr valign="top" class="fb_setting_input_shortest"<?php echo $shortest_class; ?>>
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_shortest_api_token]"><?php _e( 'Shorte.st API Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[fb_shortest_api_token]" id="wpw_auto_poster_options[fb_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['fb_shortest_api_token'] ); ?>" class="large-text">
									</td>
								</tr>
							 	<tr valign="top" class="fb_setting_input_g_shortner" <?php echo $google_shortner_cls; ?>>
	                                <th scope="row">
	                                    <label for="wpw_auto_poster_options[fb_google_short_api_key]"><?php _e('Google API Key', 'wpwautoposter'); ?> </label>
	                                </th>
	                                <td>
	                                    <input type="text" name="wpw_auto_poster_options[fb_google_short_api_key]" id="wpw_auto_poster_options[fb_google_short_api_key]" value="<?php echo !empty($wpw_auto_poster_options["fb_google_short_api_key"])?($model->wpw_auto_poster_escape_attr($wpw_auto_poster_options["fb_google_short_api_key"])):''; ?>" class="large-text">
	                                    <p><small><?php _e( 'Enter Google Plus API Key. You need to enable <b>URL Shortener API</b>  in google plus application', 'wpwautoposter' ); ?></small></p>
	                                </td>
	                            </tr>
								
								<?php
									echo apply_filters ( 
														 'wpweb_fb_settings_submit_button', 
														 '<tr valign="top">
																<td colspan="2">
																	<input type="submit" value="' . __( 'Save Changes', 'wpwautoposter' ) . '" id="wpw_auto_poster_set_submit" name="wpw_auto_poster_set_submit" class="button-primary">
																</td>
															</tr>'
														);
								?>
							</tbody>
						</table>
									
					</div><!-- .inside -->
							
			</div><!-- #facebook_general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-facebook-general -->
<!-- end of the facebook general settings meta box -->

<!-- beginning of the facebook api settings meta box -->
<div id="wpw-auto-poster-facebook-api" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="facebook_api" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'Facebook API Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table wpw-auto-poster-facebook-settings">											
							<tbody>				
								<tr valign="top">
									<td scope="row">
										<strong><label><?php _e( 'Facebook Application:', 'wpwautoposter' ); ?></label></strong>
									</td>
									<td colspan="3">
										<p><?php //_e( 'If you already created a Facebook Application for this site, then you can use the same App ID/API Key and App Secret here.', 'wpwautoposter' ); ?>
										<?php _e( 'Before you can start publishing your content to Facebook you need to create a Facebook Application.', 'wpwautoposter' ); ?>
										</p> 
										<p><?php printf( __('You can get a step by step tutorial on how to create a Facebook Application on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="http://wpweb.co.in/documents/social-network-integration/facebook/" target="_blank">', '</a>' ); ?></p> 
									</td>
								</tr>
								
								<tr>
									<td scope="row">
										<strong><label><?php _e( 'Allowing permissions', 'wpwautoposter' ); ?></label></strong>
									</td>
									<td colspan="3">
										<p><?php _e( 'Posting content to your chosen Facebook Fan Page or personal account requires you to grant extended permissions. If you want to use this feature you should grant the extended permissions now.', 'wpwautoposter' ); ?></p>
									</td>
								</tr> 
								
								<tr>
									<td colspan="3">
										<p class="wpw-auto-poster-info-box"><?php _e( '<b>Note: </b>Please note the Facebook App, Facebook profile or page and the user who authorizes the app MUST belong to the <b>same Facebook account</b>. So please make sure you are logged in to Facebook as the same user who created the app.', 'wpwautoposter' ); ?></p>
									</td>
								</tr>
								<tr valign="top">
									<td scope="row">
										<strong><label for="wpw_auto_poster_options[facebook_keys][0][app_id]"><?php _e( 'Facebook App ID/API Key', 'wpwautoposter' ); ?></label></strong>
									</td>
									<td scope="row">
										<strong><label for="wpw_auto_poster_options[facebook_keys][0][app_secret]"><?php _e( 'Facebook App Secret', 'wpwautoposter' ); ?></label></strong>
									</td>
									<td scope="row">
										<strong><label><?php _e( 'Allowing permissions', 'wpwautoposter' ); ?></label></strong>
									</td>
									<td></td>
								</tr>
								
							<?php
							
							if( !empty( $facebook_keys ) ) {
								
								foreach ( $facebook_keys as $facebook_key => $facebook_value ) {
									
									if( !isset( $facebook_key ) ) {
										$facebook_key = "0";
									}

									// Don't disply delete link for first row
									$facebook_delete_class = empty( $facebook_key ) ? '' : ' wpw-auto-poster-display-inline ';
							?>
								<tr valign="top" class="wpw-auto-poster-facebook-account-details" data-row-id="<?php echo $facebook_key; ?>">
									<td scope="row" width="30%">
										<input type="text" name="wpw_auto_poster_options[facebook_keys][<?php echo $facebook_key; ?>][app_id]" value="<?php echo $model->wpw_auto_poster_escape_attr( $facebook_value['app_id'] ); ?>" class="large-text wpw-auto-poster-facebook-app-id" />
										<p><small><?php _e( 'Enter Facebook App ID / API Key.', 'wpwautoposter' ); ?></small></p>  
									</td>
									<td scope="row" width="30%">
										<input type="text" name="wpw_auto_poster_options[facebook_keys][<?php echo $facebook_key; ?>][app_secret]" value="<?php echo $model->wpw_auto_poster_escape_attr( $facebook_value['app_secret'] ); ?>" class="large-text wpw-auto-poster-facebook-app-secret" />
										<p><small><?php _e( 'EnterFacebook App Secret.', 'wpwautoposter' ); ?></small></p>  
									</td>
									<td scope="row" width="40%" valign="top" class="wpw-grant-reset-data">
										<?php
											
											if( !empty($facebook_value['app_id']) && !empty($facebook_value['app_secret']) && !empty($wpw_auto_poster_fb_sess_data[ $facebook_value['app_id'] ]) )  {
												
												echo '<p>' . __( 'You already granted extended permissions.', 'wpwautoposter' ) . '</p>';	
										?>
												<a href="<?php echo add_query_arg( array( 'page' => 'wpw-auto-poster-settings', 'fb_reset_user' => '1', 'wpw_fb_app' => $facebook_value['app_id'] ), admin_url( 'admin.php' ) );?>"><?php _e( 'Reset User Session', 'wpwautoposter' ); ?></a>
										<?php
											} elseif( !empty($facebook_value['app_id']) && !empty($facebook_value['app_secret']) ) {
												echo '<p><a href="' . $fbposting->wpw_auto_poster_get_fb_login_url( $facebook_value['app_id'] ) . '">' . __( 'Grant extended permissions', 'wpwautoposter' ) . '</a></p>';
											}
										?>
									</td>
									<td>
										<a href="javascript:void(0);" class="wpw-auto-poster-delete-fb-account wpw-auto-poster-facebook-remove <?php echo $facebook_delete_class; ?>" title="<?php _e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo WPW_AUTO_POSTER_META_URL; ?>/images/delete-16.png" alt="<?php _e('Delete','wpwautoposter'); ?>"/></a>
									</td>
								</tr>
							<?php 
								}
							} else {
							?>
								<tr valign="top" class="wpw-auto-poster-facebook-account-details" data-row-id="<?php echo (empty($facebook_key) ? '': $facebook_key); ?>">
									<td scope="row" width="30%">
										<input type="text" name="wpw_auto_poster_options[facebook_keys][0][app_id]" value="" class="large-text wpw-auto-poster-facebook-app-id" />
										<p><small><?php _e( 'Enter Facebook App ID / API Key.', 'wpwautoposter' ); ?></small></p>  
									</td>
									<td scope="row" width="30%">
										<input type="text" name="wpw_auto_poster_options[facebook_keys][0][app_secret]" value="" class="large-text wpw-auto-poster-facebook-app-secret" />
										<p><small><?php _e( 'EnterFacebook App Secret.', 'wpwautoposter' ); ?></small></p>  
									</td>
									<td scope="row" width="40%" valign="top" class="wpw-grant-reset-data"></td>
									<td>
										<a href="javascript:void(0);" class="wpw-auto-poster-delete-fb-account wpw-auto-poster-facebook-remove" title="<?php _e( 'Delete', 'wpwautoposter' ); ?>"><img src="<?php echo WPW_AUTO_POSTER_META_URL; ?>/images/delete-16.png" alt="<?php _e('Delete','wpwautoposter'); ?>"/></a>
									</td>
								</tr>
							<?php } ?>
							
								<tr>
									<td colspan="4">
										<a class='wpw-auto-poster-add-more-fb-account button' href='javascript:void(0);'><?php _e( 'Add more', 'wpwautoposter' ); ?></a>
									</td>
								</tr> 
								
								<tr valign="top">
									<td scope="row">
										<label for="wpw_auto_poster_options[prevent_linked_accounts_access]"><?php _e( 'Prevent access to my linked accounts:', 'wpwautoposter' ); ?></label>
									</td>
									
									<td  valign="top" colspan="3">
										<input name="wpw_auto_poster_options[prevent_linked_accounts_access]" id="wpw_auto_poster_options[prevent_linked_accounts_access]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_linked_accounts_access'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_linked_accounts_access'] ); } ?> />
										<p><small><?php _e( 'If you check this option, then all your linked Facebook Accounts won\'t be accessible to the plugin. This means that you then will only be able to post to your personal Facebook Account/Profile.','wpwautoposter' ); ?></small></p>
									</td>	
								</tr>
								
								<?php
									echo apply_filters ( 
														 'wpweb_fb_settings_submit_button', 
														 '<tr valign="top">
																<td colspan="4">
																	<input type="submit" value="' . __( 'Save Changes', 'wpwautoposter' ) . '" id="wpw_auto_poster_set_submit" name="wpw_auto_poster_set_submit" class="button-primary">
																</td>
															</tr>'
														);
								?>
							</tbody>
						</table>
									
					</div><!-- .inside -->
							
			</div><!-- #facebook_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-facebook-api -->
<!-- end of the facebook api settings meta box -->

<?php if( isset($wpw_auto_poster_options['app_id']) && !empty($wpw_auto_poster_options['app_id']) && isset($wpw_auto_poster_options['app_secret']) && !empty($wpw_auto_poster_options['app_secret'])  ) { ?>


<?php } ?>

<!-- beginning of the autopost to facebook meta box -->
<div id="wpw-auto-poster-autopost-facebook" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="autopost_facebook" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'Autopost to Facebook', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table">											
							<tbody>
							
								<tr valign="top"> 
									<th scope="row">
										<label for="wpw_auto_poster_options[prevent_post_metabox]"><?php _e( 'Do not allow individual posts to Facebook:', 'wpwautoposter' ); ?></label>
									</th>									
									<td>
										<input name="wpw_auto_poster_options[prevent_post_metabox]" id="wpw_auto_poster_options[prevent_post_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_metabox'] ); } ?> />
										<p><small><?php _e( 'If you run a multi author blog, then you can prevent your authors to posting to individual Facebook Accounts by checking this box. If checked, then all posts, created by any author, will get posted to your chosen Facebook Account.', 'wpwautoposter' ); ?></small></p>
									</td>	
								</tr>
										
								<?php
									if( isset( $_SESSION['wpweb_fb_user_accounts'] ) && !empty( $_SESSION['wpweb_fb_user_accounts'] ) ) {
										$wpw_auto_poster_fb_user = $fbposting->wpw_auto_poster_get_fb_user_data();
									} else {
										$wpw_auto_poster_fb_user = '';
									}
									
									if( empty( $wpw_auto_poster_fb_user['id'] ) ) {
										$wpw_auto_poster_fb_user['id'] = 0;
									}						
										
									$types = get_post_types( array( 'public'=>true ), 'objects' );
									$types = is_array( $types ) ? $types : array();
								?>
								<tr valign="top">
									<th scope="row">
										<label><?php _e( 'Map WordPress types to Facebook locations:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										
											<?php
												
												// Getting facebook all accounts
												$fb_accounts = wpw_auto_poster_get_fb_accounts( 'all_app_users_with_name' );
												
												foreach( $types as $type ) {
													
													if( !is_object( $type ) ) continue;
													
														if( isset( $wpw_auto_poster_options['fb_type_' . $type->name . '_method'] ) ) {
															$wpw_auto_poster_fb_type_method = $wpw_auto_poster_options['fb_type_' . $type->name . '_method'];	
														} else {
															$wpw_auto_poster_fb_type_method = '';
														}
														$label = @$type->labels->name ? $type->labels->name : $type->name;
														
														if( $label == 'Media' || $label == 'media' ) continue; // skip media
													?>
													<div class="wpw-auto-poster-fb-types-wrap">
														<div class="wpw-auto-poster-fb-types-label">
															<?php	_e( 'Autopost', 'wpwautoposter' ); 
																	echo ' '.$label; 
																	_e( ' to Facebook', 'wpwautoposter' ); 
															?>
														</div><!--.wpw-auto-poster-fb-types-label-->
														<div class="wpw-auto-poster-fb-type">
															<select name="wpw_auto_poster_options[fb_type_<?php echo $type->name; ?>_method]" id="wpw_auto_poster_fb_type_post_method">
																<?php /* <option value="0"><?php _e( 'Don\'t post this type to Facebook', 'wpwautoposter' ); ?></option>*/?>
															<?php
																foreach ( $wall_post_methods as $method_key => $method_value ) {
																	echo '<option value="' . $method_key . '" ' . selected( $wpw_auto_poster_fb_type_method, $method_key, false ) . '>' . $method_value . '</option>';
																}
															?>
															</select>
														</div><!--.wpw-auto-poster-fb-type-->
														<div class="wpw-auto-poster-fb-user-label">
															<?php _e( 'of this user', 'wpwautoposter' ); ?>(<?php _e( 's', 'wpwautoposter' );?>)
														</div><!--.wpw-auto-poster-fb-user-label-->
														<div class="wpw-auto-poster-fb-users-acc">
															<?php
																if( isset( $wpw_auto_poster_options['fb_type_'.$type->name.'_user'] ) ) {
																	$wpw_auto_poster_fb_type_user = $wpw_auto_poster_options['fb_type_'.$type->name.'_user'];	 
																} else {
																	$wpw_auto_poster_fb_type_user = '';
																}
																
																$wpw_auto_poster_fb_type_user = ( array ) $wpw_auto_poster_fb_type_user;
															?>
															
															<select name="wpw_auto_poster_options[fb_type_<?php echo $type->name; ?>_user][]" multiple="multiple">
																<?php
																if( !empty($fb_accounts) && is_array($fb_accounts) ) {
																	
																	foreach( $fb_accounts as $aid => $aval ) {
																		
																		if( is_array( $aval ) ) {
																			$fb_app_data 	= isset( $wpw_auto_poster_fb_sess_data[$aid] ) ? $wpw_auto_poster_fb_sess_data[$aid] : array();
																			$fb_user_data 	= isset($fb_app_data['wpw_auto_poster_fb_user_cache']) ? $fb_app_data['wpw_auto_poster_fb_user_cache'] : array();
																			$fb_opt_label	= !empty( $fb_user_data['name'] ) ? $fb_user_data['name'] .' - ' : '';
																			$fb_opt_label	= $fb_opt_label . $aid;
																	?>
																			<optgroup label="<?php echo $fb_opt_label; ?>">
																			
																			<?php foreach ( $aval as $aval_key => $aval_data ) { ?>
																				<option value="<?php echo $aval_key; ?>" <?php selected( in_array( $aval_key, $wpw_auto_poster_fb_type_user ), true, true ); ?> ><?php echo $aval_data; ?></option>
																			<?php } ?>
																			
																			</optgroup>
																			
																<?php	} else { ?>
																				<option value="<?php echo $aid; ?>" <?php selected( in_array( $aid, $wpw_auto_poster_fb_type_user ), true, true ); ?> ><?php echo $aval; ?></option>
																<?php 	}
																	
																	} // End of foreach
																} // End of main if
																?>
															</select>
														</div><!--.wpw-auto-poster-fb-users-acc-->
													</div><!--.wpw-auto-poster-fb-types-wrap-->
											<?php } ?>
										
									</td>
								</tr> 
								<?php if( $fb_app_version < 209 ) { ?>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_custom_img]"><?php _e( 'Post Image:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input type="text" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['fb_custom_img'] ); ?>" name="wpw_auto_poster_options[fb_custom_img]" id="wpw_auto_poster_options_fb_custom_img" class="large-text wpw-auto-poster-img-field">
										<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php _e( 'Add Image','wpwautoposter' );?>" />
										<p><small><?php _e( 'Here you can upload a default image which will be used for the Facebook wall post.', 'wpwautoposter' ); ?></small></p><br>
										<p><small><strong><?php _e('Note: ', 'wpwautoposter'); ?></strong><?php _e( 'This option only work if your facebook app version is below 2.9. If you\'re using latest facebook app, it wont work.', 'wpwautoposter' );?> <a href="https://developers.facebook.com/blog/post/2017/06/27/API-Change-Log-Modifying-Link-Previews/" target="_blank"><?php _e('Learn More.', 'wpwautoposter');?></a></small></p>
									</td>	
								</tr>
								<?php } ?>
								<tr valign="top">									
									<th scope="row">
										<label for="wpw_auto_poster_options[fb_global_message_template]"><?php _e( 'Custom Message:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<textarea type="text" name="wpw_auto_poster_options[fb_global_message_template]" id="wpw_auto_poster_options[fb_global_message_template]" class="large-text"><?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['fb_global_message_template'] ); ?></textarea>
										<p><small style="line-height: 20px;"><?php _e( 'Here you can enter default message which will be used for the wall post. Leave it empty to use the post level message. You can use following template tags within the message template:', 'wpwautoposter' ); ?>
										<?php 
										$fb_template_str = '<br /><code>{first_name}</code> - ' . __('displays the first name,', 'wpwautoposter') .
							            '<br /><code>{last_name}</code> - ' . __('displays the last name,', 'wpwautoposter') .
							            '<br /><code>{title}</code> - ' . __('displays the default post title,', 'wpwautoposter') .
							            '<br /><code>{link}</code> - ' . __('displays the default post link,', 'wpwautoposter') .
							            '<br /><code>{sitename}</code> - ' . __('displays the name of your site,', 'wpwautoposter') .
							            '<br /><code>{excerpt}</code> - ' . __('displays the post excerpt.', 'wpwautoposter').
							            '<br /><code>{hashtags}</code> - ' . __('displays the post tags as hashtags.', 'wpwautoposter').
							            '<br /><code>{hashcats}</code> - ' . __('displays the post categories as hashtags.', 'wpwautoposter');
							            print $fb_template_str;
							            ?>
										</small></p>
									</td>	
									
								</tr>
								
								<?php
									echo apply_filters ( 
														 'wpweb_fb_settings_submit_button', 
														 '<tr valign="top">
																<td colspan="2">
																	<input type="submit" value="' . __( 'Save Changes', 'wpwautoposter' ) . '" id="wpw_auto_poster_set_submit" name="wpw_auto_poster_set_submit" class="button-primary">
																</td>
															</tr>'
														);
								?>
							</tbody>
						</table>
									
					</div><!-- .inside -->
							
			</div><!-- #autopost_facebook -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #ps-poster-autopost-facebook -->
<!-- end of the autopost to facebook meta box -->