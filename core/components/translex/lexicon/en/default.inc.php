<?php
$_lang['translex.header'] = 'TransleX';
$_lang['translex.intro'] = 'Use the form below to load and edit the required lexicon';

$_lang['translex.please_choose'] = 'Please choose...';
$_lang['translex.default'] = 'Default';
$_lang['translex.package'] = 'Package';
$_lang['translex.language'] = 'Language';
$_lang['translex.topic'] = 'Topic';
$_lang['translex.user'] = 'User';
$_lang['translex.user_anonymous'] = 'Anonymous User';
$_lang['translex.user_remote_host'] = 'User IP';
$_lang['translex.email_unknown'] = 'Unknown Email';

/* Interface */
$_lang['translex.select_package'] = 'Package';
$_lang['translex.select_language'] = 'Language';
$_lang['translex.select_topic'] = 'Topic';

$_lang['translex.btn_save'] = 'Save';
$_lang['translex.btn_make_live'] = 'Make Live';

$_lang['translex.status_saving'] = 'Saving, please wait...';

$_lang['translex.event_accessed'] = 'Accessed';
$_lang['translex.event_committed'] = 'Committed';
$_lang['translex.event_error'] = 'Error';
$_lang['translex.event_saved'] = 'Saved';
$_lang['translex.event_viewed'] = 'Viewed';



$_lang['translex.load_lexicon'] = 'Load Lexicon';
$_lang['translex.table_heading_key'] = 'Key';
$_lang['translex.table_heading_value'] = 'Value';
$_lang['translex.fetching_topics'] = 'Fetching topics, please wait...';
$_lang['translex.fetching_keys_and_values'] = 'Fetching keys and values for default topic, please wait...';
$_lang['translex.retrieving_language_entries'] = 'Retrieving language entries, please wait...';
$_lang['translex.translate_value_above'] = 'Translate the value above to';
$_lang['translex.contribute'] = 'Contribute';
$_lang['translex.current_live_value'] = 'Current Live Value';
$_lang['translex.made_live_message'] = 'This lexicon has been made live';
$_lang['translex.top_link_text'] = 'Return to Top';
$_lang['translex.admin_notify_email'] = '<p>Hi,</p><p>This is a notification sent by Translex from [[+site_name]] to inform you of the following update:</p>
<p><b>Package:</b>&nbsp;[[+package]]<br/><b>Topic:</b>&nbsp;[[+topic]]<br/><b>Language:</b>&nbsp;[[+lang]]<br/><b>Action:</b>&nbsp;[[+action]]</p><p>These changes were made by:
<br/><b>Name:</b>&nbsp;[[+name]]<br/><b>Email:</b>&nbsp;[[+email]]</p>';
$_lang['translex.admin_notify_email_subject'] = 'TransleX Notification ([[+site_name]])';
$_lang['translex.load_log_file'] = 'Load Log File';
$_lang['translex.reload_log_file'] = 'Reload Log File';
$_lang['translex.loading_log_file'] = 'Loading log file, please wait...';
$_lang['translex.clearing_log_file'] = 'Clearing log file...';
$_lang['translex.clear_log_file'] = 'Clear Log File';


/* Success messages */
$_lang['translex.success_save_completed'] = 'The lexicon topic changes were saved successfully';
$_lang['translex.success_backup_completed'] = 'Backup of live topic file was completed successfully';
$_lang['translex.success_commit_completed'] = 'The live topic file has been successfully updated';
$_lang['translex.success_log_file_cleared'] = 'The log file has been cleared';
$_lang['translex.success_log_file_empty'] = 'The log file is currently empty';

/* Error messages */
$_lang['translex.error_admin_notify_failed'] = 'Notification e-mail to the administrator could not be sent';
$_lang['translex.error_create_workspacedir_failed'] = 'Workspace directory could not be created';
$_lang['translex.error_create_workspacepkgdir_failed'] = 'Package directory could not be created in the workspace directory';
$_lang['translex.error_create_workspacelangdir_failed'] = 'Language directory could not be created in the package directory';
$_lang['translex.error_create_topicfile'] = 'Topic file for this language could not be created';
$_lang['translex.error_create_logfile_failed'] = 'Log file could not be created';
$_lang['translex.error_remove_empty_topicfile_failed'] = 'Could not remove an empty topic file from the working directory';
$_lang['translex.error_no_package_selected'] = 'No package selected';
$_lang['translex.error_no_default_language'] = 'This package has no lexicon for the default language';
$_lang['translex.error_no_default_topics'] = 'This package has no lexicon topics for the default language';
$_lang['translex.error_no_default_topic_entries'] = 'The selected topic has no entries in the default language';
$_lang['translex.error_save_failed'] = 'Failed to save changes to the lexicon topic';
$_lang['translex.error_backup_failed'] = 'Failed to take backup of live topic file. Commit has been cancelled.';
$_lang['translex.error_commit_failed'] = 'Replacing the current live topic file failed';
$_lang['translex.error_writing_topic_file_failed'] = 'Failed to write topic entries to file';
$_lang['translex.error_logfile_does_not_exist'] = 'A log file does not exist';

/* System Settings */
$_lang['setting_translex.languages'] = 'Available Languages';
$_lang['setting_translex.languages_desc'] = 'A comma-separated list of languages that packages will be able to be translated into.';
$_lang['setting_translex.request_param_action'] = 'Action Request Parameter';
$_lang['setting_translex.request_param_action_desc'] = 'Used to change the parameter that defines which action to fire. Can be changed to avoid conflicts.';
$_lang['setting_translex.request_param_obtain'] = 'Obtain Request Parameter';
$_lang['setting_translex.request_param_obtain_desc'] = 'Used to change the parameter that defines which type of data to fetch. Can be changed to avoid conflicts.';
$_lang['setting_translex.request_param_package'] = 'Package Request Parameter';
$_lang['setting_translex.request_param_package_desc'] = 'Used to change the parameter that defines which package to load topics for. Can be changed to avoid conflicts.';
$_lang['setting_translex.request_param_topic'] = 'Topic Request Parameter';
$_lang['setting_translex.request_param_topic_desc'] = 'Used to change the parameter that defines which topic to load entries from. Can be changed to avoid conflicts.';
$_lang['setting_translex.request_param_language'] = 'Language Request Parameter';
$_lang['setting_translex.request_param_language_desc'] = 'Used to change the parameter that defines which language to load topics for. Can be changed to avoid conflicts.';