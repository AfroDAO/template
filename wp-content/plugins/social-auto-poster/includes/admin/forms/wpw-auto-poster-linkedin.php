<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * LinkedIn Settings
 *
 * The html markup for the LinkedIn settings tab.
 *
 * @package Social Auto Poster
 * @since 1.0.0
 */

global $wpw_auto_poster_options, $wpw_auto_poster_model, $wpw_auto_poster_li_posting;

//model class
$model = $wpw_auto_poster_model;

//linkedin posting class
$liposting = $wpw_auto_poster_li_posting;

$li_wp_pretty_url = ( !empty( $wpw_auto_poster_options['li_wp_pretty_url'] ) ) ? $wpw_auto_poster_options['li_wp_pretty_url'] : '';

$li_wp_pretty_url = !empty( $li_wp_pretty_url ) ? ' checked="checked"' : '';
$li_wp_pretty_url_css = ( $wpw_auto_poster_options['li_url_shortener'] == 'wordpress' ) ? ' display:table-row': ' display:none';

// get url shortner service list array 
$li_url_shortener = $model->wpw_auto_poster_get_shortner_list();
$li_exclude_cats = array();
?>

<!-- beginning of the linkedin general settings meta box -->
<div id="wpw-auto-poster-linkedin-general" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="linkedin_general" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
								
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'LinkedIn General Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
						<table class="form-table">											
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_linkedin]"><?php _e( 'Enable Autoposting to LinkedIn:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input name="wpw_auto_poster_options[enable_linkedin]" id="wpw_auto_poster_options[enable_linkedin]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['enable_linkedin'] ) ) { checked( '1', $wpw_auto_poster_options['enable_linkedin'] ); } ?> />
										<p><small><?php _e( 'Check this box, if you want to automatically post your new content to LinkedIn.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>	

								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[enable_linkedin_for]"><?php _e( 'Enable LinkedIn Autoposting for:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<ul>
										<?php 
											$all_types = get_post_types( array( 'public' => true ), 'objects');
											$all_types = is_array( $all_types ) ? $all_types : array();
											
											if( !empty( $wpw_auto_poster_options['enable_linkedin_for'] ) ) {
												$prevent_meta = $wpw_auto_poster_options['enable_linkedin_for'];
											} else {
												$prevent_meta = '';
											}
															
											$prevent_meta = is_array( $prevent_meta ) ? $prevent_meta : array();

											if( !empty( $wpw_auto_poster_options['li_post_type_tags'] ) ) {
												$li_post_type_tags = $wpw_auto_poster_options['li_post_type_tags'];
											} else {
												$li_post_type_tags = array();
											}

											$static_post_type_arr = wpw_auto_poster_get_static_tag_taxonomy();

											if( !empty( $wpw_auto_poster_options['li_post_type_cats'] ) ) {
												$li_post_type_cats = $wpw_auto_poster_options['li_post_type_cats'];
											} else {
												$li_post_type_cats = array();
											}

											// Get saved categories for linkedin to exclude from posting
											if( !empty( $wpw_auto_poster_options['li_exclude_cats'] ) ) {
												$li_exclude_cats = $wpw_auto_poster_options['li_exclude_cats'];
											} 
														
											foreach( $all_types as $type ) {	
																											
												if( !is_object( $type ) ) continue;															
													$label = @$type->labels->name ? $type->labels->name : $type->name;
													if( $label == 'Media' || $label == 'media' ) continue; // skip media
													$selected = ( in_array( $type->name, $prevent_meta ) ) ? 'checked="checked"' : '';
										?>
															
											<li class="wpw-auto-poster-prevent-types">
												<input type="checkbox" id="wpw_auto_posting_linkedin_prevent_<?php echo $type->name; ?>" name="wpw_auto_poster_options[enable_linkedin_for][]" value="<?php echo $type->name; ?>" <?php echo $selected; ?>/>
																						
												<label for="wpw_auto_posting_linkedin_prevent_<?php echo $type->name; ?>"><?php echo $label; ?></label>
											</li>
											
											<?php	} ?>
										</ul>
										<p><small><?php _e( 'Check each of the post types that you want to post automatically to LinkedIn when they get published.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>


								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_post_type_tags][]"><?php _e( 'Select Tag:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[li_post_type_tags][]" id="wpw_auto_poster_options[li_post_type_tags]" class="li_post_type_tags wpw-auto-poster-cats-tags-select" multiple="multiple">
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
										                	if(isset($li_post_type_tags[$type->name]) && !empty($li_post_type_tags[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $li_post_type_tags[$type->name] ) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical != 1) {

										                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader"><img src="<?php echo WPW_AUTO_POSTER_IMG_URL."/icons/ajax-loader.gif";?>"/></div>
										<p><small><?php _e( 'Select the Tags for each post type that you want to post as ', 'wpwautoposter' ); ?><b><?php _e('hashtags.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr> 
								
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_post_type_cats][]"><?php _e( 'Select Categories:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[li_post_type_cats][]" id="wpw_auto_poster_options[li_post_type_cats]" class="li_post_type_cats wpw-auto-poster-cats-tags-select" multiple="multiple">
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
										                	if(isset($li_post_type_cats[$type->name]) && !empty($li_post_type_cats[$type->name])) {
										                		$selected = ( in_array( $taxonomy->name, $li_post_type_cats[$type->name]) ) ? 'selected="selected"' : '';
										                	}
										                    if (is_object($taxonomy) && $taxonomy->hierarchical == 1) {

										                        echo '<option value="' . $type->name."|".$taxonomy->name . '" '.$selected.'>'.$taxonomy->label.'</option>';
										                    }
										                }
										                echo '</optgroup>';
										            }
											}?>
										</select>
										<div class="wpw-ajax-loader"><img src="<?php echo WPW_AUTO_POSTER_IMG_URL."/icons/ajax-loader.gif";?>"/></div>
										<p><small><?php _e( 'Select the Categories for each post type that you want to post categories as ', 'wpwautoposter' ); ?><b><?php _e('hashtags.', 'wpwautoposter' );?></b></small></p>
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_exclude_cats][]"><?php _e( 'Exclude Specific Categories:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[li_exclude_cats][]" id="wpw_auto_poster_options[li_exclude_cats]" class="li_exclude_cats wpw-auto-poster-cats-exclude-select" multiple="multiple">
											
											<?php

												$post_type_categories = wpw_auto_poster_get_all_categories();

												if(!empty($post_type_categories)) {
												
													foreach($post_type_categories as $post_type => $post_data){

														echo '<optgroup label="'.$post_data['label'].'">';

														if(isset($post_data['categories']) && !empty($post_data['categories']) && is_array($post_data['categories'])){
															
															foreach($post_data['categories'] as $cat_slug => $cat_name){

																$selected ='';
																if( !empty($li_exclude_cats[$post_type] ) ) {
											                		$selected = ( in_array( $cat_slug, $li_exclude_cats[$post_type] ) ) ? 'selected="selected"' : '';
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
										<label for="wpw_auto_poster_options[li_url_shortener]"><?php _e( 'URL Shortener:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<select name="wpw_auto_poster_options[li_url_shortener]" id="wpw_auto_poster_options[li_url_shortener]" class="li_url_shortener" data-content='li'>
											<?php
																
												foreach ( $li_url_shortener as $key => $option ) {											
													?>
													<option value="<?php echo $model->wpw_auto_poster_escape_attr( $key ); ?>" <?php selected( $wpw_auto_poster_options['li_url_shortener'], $key ); ?>>
														<?php esc_html_e( $option ); ?>
													</option>
													<?php
												}															
											?> 														
										</select>
										<p><small><?php _e( 'Long URLs will automatically be shortened using the specified URL shortener.', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>
								
								<tr id="row-li-wp-pretty-url" valign="top" style="<?php print $li_wp_pretty_url_css;?>">
									<th scope="row">
										<label for="wpw_auto_poster_options[li_wp_pretty_url]"><?php _e( 'Pretty permalink URL:', 'wpwautoposter' ); ?></label> 
									</th>
									<td>
										<input type="checkbox" name="wpw_auto_poster_options[li_wp_pretty_url]" id="wpw_auto_poster_options[li_wp_pretty_url]" class="li_wp_pretty_url" data-content='li' value="yes" <?php print $li_wp_pretty_url;?>>
										<p><small><?php _e( 'Check this box if you want to use pretty permalink. i.e. http://example.com/test-post/. (Not Recommnended).', 'wpwautoposter' ); ?></small></p>
									</td>
								</tr>

								<?php	        
									if( $wpw_auto_poster_options['li_url_shortener'] == 'bitly' ) {	        		
										$class = '';	        		
									} else {	        		
										$class = ' style="display:none;"';
									}
									
									if( $wpw_auto_poster_options['li_url_shortener'] == 'shorte.st' ) {
										$shortest_class = '';	        		
									} else {	        		
										$shortest_class = ' style="display:none;"';
									}
									
									if ($wpw_auto_poster_options['li_url_shortener'] == 'google_shortner') {
		                                $google_shortner_cls = '';
		                            } else {
		                                $google_shortner_cls = ' style="display:none;"';
		                            }
								?>
								
								<tr valign="top" class="li_setting_input_bitly"<?php echo $class; ?>>
									<th scope="row">
										<label for="wpw_auto_poster_options[li_bitly_access_token]"><?php _e( 'Bit.ly Access Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[li_bitly_access_token]" id="wpw_auto_poster_options[li_bitly_access_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_bitly_access_token'] ); ?>" class="large-text">
									</td>
								</tr>
								
								<tr valign="top" class="li_setting_input_shortest"<?php echo $shortest_class; ?>>
									<th scope="row">
										<label for="wpw_auto_poster_options[li_shortest_api_token]"><?php _e( 'Shorte.st API Token', 'wpwautoposter' ); ?> </label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[li_shortest_api_token]" id="wpw_auto_poster_options[li_shortest_api_token]" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_shortest_api_token'] ); ?>" class="large-text">
									</td>
								</tr>
							 	<tr valign="top" class="li_setting_input_g_shortner" <?php echo $google_shortner_cls; ?>>
	                                <th scope="row">
	                                    <label for="wpw_auto_poster_options[li_google_short_api_key]"><?php _e('Google API Key', 'wpwautoposter'); ?> </label>
	                                </th>
	                                <td>
	                                    <input type="text" name="wpw_auto_poster_options[li_google_short_api_key]" id="wpw_auto_poster_options[li_google_short_api_key]" value="<?php echo !empty($wpw_auto_poster_options["li_google_short_api_key"])?($model->wpw_auto_poster_escape_attr($wpw_auto_poster_options["li_google_short_api_key"])):''; ?>" class="large-text">
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
									
			</div><!-- #linkedin_general -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkedin-general -->
<!-- end of the linkedin general settings meta box -->

<!-- beginning of the linkedin api settings meta box -->
<div id="wpw-auto-poster-linkedin-api" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="linkedin_api" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'LinkedIn API Settings', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
					
						<table class="form-table">
							<tbody>
								<tr valign="top">
									<th scope="row">
										<label><?php _e( 'LinkedIn App Settings:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<p><?php _e( 'Before you can start publishing your content to LinkedIn you need to create a LinkedIn Application.', 'wpwautoposter' ); ?></p>
										<p><?php printf( __('You can get a step by step tutorial on how to create a LinkedIn Application on our %sDocumentation%s.', 'wpwautoposter' ), '<a href="http://wpweb.co.in/documents/social-network-integration/linkedin/" target="_blank">', '</a>' ); ?></p> 
									</td>
								</tr> 
								
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[linkedin_app_id]"><?php _e( 'LinkedIn App ID/API Key:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[linkedin_app_id]" id="wpw_auto_poster_options[linkedin_app_id]" value="<?php if (isset($wpw_auto_poster_options['linkedin_app_id'])) { echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['linkedin_app_id'] ); } ?>" class="large-text" />
										<p><small><?php _e( 'Enter LinkedIn App ID/API Key.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>    
				
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options[linkedin_app_secret]"><?php _e( 'LinkedIn App Secret:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<input type="text" name="wpw_auto_poster_options[linkedin_app_secret]" id="wpw_auto_poster_options[linkedin_app_secret]" value="<?php if (isset($wpw_auto_poster_options['linkedin_app_secret'])) { echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['linkedin_app_secret'] ); } ?>" class="large-text" />
										<p><small><?php _e( 'Enter LinkedIn App Secret.', 'wpwautoposter' ); ?></small></p>  
									</td>
								</tr>
								<tr valign="top">
									<th scope="row">
										<label for="wpw_auto_poster_options_linkedin_oauth"><?php _e( 'Linkedin Callback URL:', 'wpwautoposter' ); ?></label>
									</th>
									<td>
										<span><i><?php echo site_url(). '/?wpwautoposter=linkedin'?></i></span>
										<p><small><?php _e( 'Enter this URL to your linkedin app in OAuth 2.0 Redirect URLs field.', 'wpwautoposter' ); ?></small></p>  
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
							
			</div><!-- #linkedin_api -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkedin-api -->
<!-- end of the linkedin api settings meta box -->

<?php if( isset($wpw_auto_poster_options['linkedin_app_id']) && !empty($wpw_auto_poster_options['linkedin_app_id']) && isset($wpw_auto_poster_options['linkedin_app_secret']) && !empty($wpw_auto_poster_options['linkedin_app_secret'])  ) { ?>

<!-- beginning of the grant extended permission meta box -->
<div id="wpw-auto-poster-linkein-grant-permission" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="grant_permission" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
					<h3 class="hndle">
						<span style='vertical-align: top;'><?php _e( 'Grant Extended Permissions', 'wpwautoposter' ); ?></span>
					</h3>
									
					<div class="inside">
										
					<table class="form-table">											
						<tbody>				
							<tr valign="top">
								<th scope="row">
									<label><?php _e( 'Allowing permissions:', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<p><?php _e( 'Posting content to your chosen LinkedIn personal account requires you to grant extended permissions. If you want to use this feature you should grant the extended permissions now.', 'wpwautoposter' ); ?></p><?php										
										$returnurl = admin_url().'edit.php?post_type=wpw_auto_poster&page=wpw-auto-poster-settings';
										
										if( isset( $_SESSION['wpw_auto_poster_li_user_id'] ) && !empty( $_SESSION['wpw_auto_poster_li_user_id'] ) )  {
											
											echo '<p>' . __( 'You already granted extended permissions', 'wpwautoposter' ) . '</p>';?>
											<a href="<?php echo $returnurl.'&li_reset_user=1'; ?>"><?php _e( 'Reset User Session', 'wpwautoposter' ); ?></a><?php
										} else {
										
											echo '<p><a href="' . $liposting->wpw_auto_poster_get_li_login_url() . '">' . __( 'Grant extended permissions', 'wpwautoposter' ) . '</a></p>';
										}
									?>
								</td>
							</tr><br />
							<tr>
								<td colspan="2">
									<p class="wpw-auto-poster-info-box"><?php _e( '<b>Note: </b>Please note the LinkedIn App, LinkedIn profile or page and the user who authorizes the app MUST belong to the <b>same LinkedIn account</b>. So please make sure you are logged in to LinkedIn as the same user who created the app.', 'wpwautoposter' ); ?></p>
								</td>
							</tr><?php
								
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
			</div><!-- #grant_permissions -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkein-grant-permission -->
<!-- end of the grant extended permissions meta box -->

<?php } ?>

<!-- beginning of the grant extended permission meta box -->
<div id="wpw-auto-poster-linkein-grant-permission" class="post-box-container">
	<div class="metabox-holder">	
		<div class="meta-box-sortables ui-sortable">
			<div id="grant_permission" class="postbox">	
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'wpwautoposter' ); ?>"><br /></div>
									
				<h3 class="hndle">
					<span style='vertical-align: top;'><?php _e( 'Autopost to LinkedIn', 'wpwautoposter' ); ?></span>
				</h3>
								
				<div class="inside">
					<table class="form-table">
						<tbody>
							
							<tr valign="top"> 
								<th scope="row">
									<label for="wpw_auto_poster_options[prevent_post_li_metabox]"><?php _e( 'Do not allow individual posts to LinkedIn:', 'wpwautoposter' ); ?></label>
								</th>									
								<td>
									<input name="wpw_auto_poster_options[prevent_post_li_metabox]" id="wpw_auto_poster_options[prevent_post_li_metabox]" type="checkbox" value="1" <?php if( isset( $wpw_auto_poster_options['prevent_post_li_metabox'] ) ) { checked( '1', $wpw_auto_poster_options['prevent_post_li_metabox'] ); } ?> />
									<p><small><?php _e( 'If you check this box, then it will hide meta settings for linkedin from individual posts.', 'wpwautoposter' ); ?></small></p>
								</td>	
							</tr>
							<?php 
								$types = get_post_types( array( 'public'=>true ), 'objects' );
								$types = is_array( $types ) ? $types : array();
							?>
							<tr valign="top">
								<th scope="row">
									<label><?php _e( 'Map WordPress types to Linkedin locations:', 'wpwautoposter' ); ?></label>
								</th>
								<td><?php
									
									foreach( $types as $type ) {
										
										if( !is_object( $type ) ) continue;
										
										$label	= @$type->labels->name ? $type->labels->name : $type->name;
										
										if( $label == 'Media' || $label == 'media' ) continue; // skip media
										
										//Get linkedin Profiles Data
										$li_profile_data	= $liposting->wpw_auto_poster_get_profiles_data();
										
										//Initilize profile
										$wpw_auto_poster_li_profile	= array();
										if( isset( $wpw_auto_poster_options['li_type_'.$type->name.'_profile'] ) ) {
											
											$wpw_auto_poster_li_profile = ( array ) $wpw_auto_poster_options['li_type_'.$type->name.'_profile'];
										}
										
										?>
										
										<div class="wpw-auto-poster-fb-types-wrap">
											<div class="wpw-auto-poster-fb-types-label"><?php	
												echo __( 'Autopost', 'wpwautoposter' ) . ' ' . $label . __( ' to Linkedin', 'wpwautoposter' );?>
											</div><!--.wpw-auto-poster-li-types-label-->
											
											<div class="wpw-auto-poster-fb-type">
												<select name="wpw_auto_poster_options[li_type_<?php echo $type->name; ?>_profile][]" id="wpw_auto_poster_li_type_post_profile" multiple="multiple"><?php
													
													if( !empty( $li_profile_data ) ) {
														foreach ( $li_profile_data as $profile_id => $profile_name ) {?>
															
															<option value="<?php echo $profile_id;?>" <?php selected( in_array( $profile_id, $wpw_auto_poster_li_profile ), true, true );?>><?php echo $profile_name;?></option><?php
														}
													}?>
													
												</select>
											</div><!--.wpw-auto-poster-fb-type-->
										</div><!--.wpw-auto-poster-fb-types-wrap--><?php
									}?>
								</td>
							</tr>
							
							<tr valign="top">
								<th scope="row">
									<label for="wpw_auto_poster_options[li_post_image]"><?php _e( 'Post Image:', 'wpwautoposter' ); ?></label>
								</th>
								<td>
									<input type="text" name="wpw_auto_poster_options[li_post_image]" id="wpw_auto_poster_options_li_post_image" class="large-text wpw-auto-poster-img-field" value="<?php echo $model->wpw_auto_poster_escape_attr( $wpw_auto_poster_options['li_post_image'] ); ?>">
									<input type="button" class="button-secondary wpw-auto-poster-uploader-button" name="wpw-auto-poster-uploader" value="<?php _e( 'Add Image','wpwautoposter' );?>" />
									<p><small><?php _e( 'Here you can upload a default image which will be used for the LinkedIn wall post.', 'wpwautoposter' ); ?></small></p>
								</td>	
							</tr><?php
							
							echo apply_filters ( 
												 'wpweb_fb_settings_submit_button', 
												 '<tr valign="top">
														<td colspan="2">
															<input type="submit" value="' . __( 'Save Changes', 'wpwautoposter' ) . '" id="wpw_auto_poster_set_submit" name="wpw_auto_poster_set_submit" class="button-primary">
														</td>
													</tr>'
												);?>
						</tbody>
					</table>
				</div><!-- .inside -->
			</div><!-- #grant_permissions -->
		</div><!-- .meta-box-sortables ui-sortable -->
	</div><!-- .metabox-holder -->
</div><!-- #wpw-auto-poster-linkein-grant-permission -->
<!-- end of the grant extended permissions meta box -->
					