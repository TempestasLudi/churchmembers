<?php
/**
 * This processor handles a request for toggling archive status.
 */
class ArchiveProcessor extends AbstractProcessor {

    public function processRequest() {
        $_SESSION['ARCHIVE-MODE'] = ($_SESSION['ARCHIVE-MODE']) ? false : true;
        print($_SESSION['ARCHIVE-MODE']);
    }
}
?>