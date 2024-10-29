<div class="wrap">
    <h2><?php echo $this->plugin->displayName; ?> &raquo; <?php esc_html_e( 'Settings', 'airchat' ); ?></h2>

    <?php
    if ( isset( $this->message ) ) {
        ?>
        <div class="updated fade"><p><?php echo $this->message; ?></p></div>
        <?php
    }
    if ( isset( $this->errorMessage ) ) {
        ?>
        <div class="error fade"><p><?php echo $this->errorMessage; ?></p></div>
        <?php
    }
    ?>

    <div id="poststuff">
    	<div id="post-body" class="metabox-holder columns-2">
    		<!-- Content -->
    		<div id="post-body-content">
				<div id="normal-sortables" class="meta-box-sortables ui-sortable">
	                <div class="postbox">
	                    <h3 class="hndle"><?php esc_html_e( 'Settings', 'airchat' ); ?></h3>

	                    <div class="inside">
		                    <form action="options-general.php?page=<?php echo $this->plugin->name; ?>" method="post">
		                    	<p>
		                    		<label for="ac-bot-id"><strong><?php esc_html_e( 'Bot ID', 'airchat' ); ?></strong></label>
		                    		<input type="text" name="ac-bot-id" id="ac-bot-id" class="widefat" style="font-family:Courier New;" value=<?php echo $this->settings['ac-bot-id']; ?>>
		                    		<?php esc_html_e( 'Please enter an ID of the bot that you would like to use on your site.', 'airchat' ); ?>
		                    	</p>
		                    	<?php wp_nonce_field( $this->plugin->name, $this->plugin->name . '_nonce' ); ?>
		                    	<p>
									<input name="submit" type="submit" name="Submit" class="button button-primary" value="<?php esc_html_e( 'Save', 'airchat' ); ?>" />
								</p>
						    </form>
	                    </div>
	                </div>
				</div>
    		</div>

    		<!-- Sidebar -->
    		<div id="postbox-container-1" class="postbox-container">
    			<?php require_once( $this->plugin->folder . '/views/sidebar.php' ); ?>
    		</div>
    	</div>
	</div>
</div>