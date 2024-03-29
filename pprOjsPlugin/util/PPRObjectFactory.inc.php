<?php

require_once(dirname(__FILE__) . '/PPRSubmissionUtil.inc.php');
require_once(dirname(__FILE__) . '/PPRFirstNamesManagementService.inc.php');
require_once(dirname(__FILE__) . '/../tasks/PPRTaskNotificationRegistry.inc.php');
/**
 * Service inject internal and OJS classes into PPR services
 * This is useful for testing
 */
class PPRObjectFactory {

    public function submissionMailTemplate($submission, $emailKey = null, $locale = null, $context = null, $includeSignature = true) {
        import('lib.pkp.classes.mail.SubmissionMailTemplate');
        return new SubmissionMailTemplate($submission, $emailKey, $locale, $context, $includeSignature);
    }

    public function accessKeyManager() {
        import('lib.pkp.classes.security.AccessKeyManager');
        return new AccessKeyManager();
    }

    public function submissionUtil() {
        return PPRSubmissionUtil::getInstance();
    }

    public function pprTaskNotificationRegistry($contextId) {
        return new PPRTaskNotificationRegistry($contextId);
    }

    public function firstNamesManagementService() {
        return new PPRFirstNamesManagementService($this->submissionUtil());
    }


}