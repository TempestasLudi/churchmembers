<?php
if (file_exists('../config/config.php')) {
  require_once '../config/config.php';
} else {
  print('No config file found');
}

//********************************* Includes ***********************************************/
require_once CLASSES_PATH . 'ProcessRequest.php';

header("content-type: application/x-javascript");
/*
This file contains some localized messages and functions for a Dutch interface.
Messages as defined as javascript constants in the following manner:
	const <key> = <value>;

Deze file bevat enkele Nederlandse systeemmeldingen.
Systeemmeldingen zijn gedefinieerd als constanten:
	const <sleutel> = <waarde>;
*/
?>
var labelChurchmembers = "<?php echo __("ChurchMembers");?>"
var noAjax = "<?php echo __("Your browser doesn't support Javascript or AJAX. You need Javascript and AJAX to run this application ");?>"
var wrongFieldSize = "<?php echo __("Use between [min] and [max] characters");?>"
var wrongEmail = "<?php echo __("Please use a valid emailaddress. ex: 'example@domain.com'");?>"
var errorDelAddress = "<?php echo __("There are still members on this address. Remove those first.");?>"
var errorDelMember = "<?php echo __("You didn't select a member to delete");?>"
var errorDelGroup = "<?php echo __("There are still members in this group. Delete those first");?>"
var errorAddMember = "<?php echo __("Create a new address before adding new members");?>"
var errorMoveMember = "<?php echo __("You didn't select a member to move");?>"
var errorArchiveMember = "<?php echo __("You didn't select a member to archive");?>"
var errorArchiveAddress = "<?php echo __("You didn't select an address to archive");?>"
var labelSearch = "<?php echo __("Searching...");?>"
var labelSave = "<?php echo __("Save");?>"
var labelEdit = "<?php echo __("Edit");?>"
var labelOk = "<?php echo __("Ok");?>"
var labelClose = "<?php echo __("Close");?>"
var labelNext = "<?php echo __("Next");?>"
var labelPrevious = "<?php echo __("Previous");?>"
var labelDelete = "<?php echo __("Delete");?>"
var labelCancel = "<?php echo __("Cancel");?>"
var labelOptions = "<?php echo __("Settings");?>"
var labelDownload = "<?php echo __("Download");?>"
var labelSend = "<?php echo __("Send");?>"
var labelPreviewMail = "<?php echo __("Preview of email");?>"
var labelSendMail = "<?php echo __("Send email");?>"
var labelFailed = "<?php echo __("Failed");?>"
var labelUpload = "<?php echo __("Select a file");?>"
var labelDropArea = "<?php echo __("Drag a file for upload");?>"
var errorExtension = "<?php echo __("{file} doesn't have a valid extension. Only {extensions} files will be accepted.");?>"
var errorSize = "<?php echo __("{file} is to large. Maximum filesize is {sizeLimit}.");?>"
var errorMinSize = "<?php echo __("{file} is to small. Minimum filesize is {minSizeLimit}.");?>"
var errorEmptyFile = "<?php echo __("{file}  is empty. Select another file and try again.");?>"
var errorOnLeave = "<?php echo __("If you leave this page your changes will not be saved. Are you sure?");?>"
var labelReadMore = "<?php echo __("Show all");?>"
var labelCreateGroup = "<?php echo __("Add Group");?>"
var labelDeleteGroup = "<?php echo __("Delete Group");?>"
var labelRenameGroup = "<?php echo __("Rename Group");?>"
var labelRemoveGrouping = "<?php echo __("Remove grouping");?>"
var labelAddGrouping = "<?php echo __("Group by address");?>"