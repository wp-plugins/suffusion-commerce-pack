$j = jQuery.noConflict();
$j(document).ready(function() {
	$j('#scp_return_message').hide();

	$j('input.button').live("click", function() {
		var name = this.name;
		if (name == 'copy_prospress') {
			if (confirm("This will overwrite any pre-existing Prospress-related files in your current theme. Are you sure you want to proceed?")) {
				$j('div.suf-loader').show();
				$j.post(ajaxurl, 'action=scp_move_template_files&plugin=prospress', function(data) {
					$j('#scp_return_message').html("The template files have been updated.").show().fadeOut(20000);
				});
			}
		}
	});
});
