<?php

import('lib.pkp.classes.plugins.GenericPlugin');

class PeerPreReviewProgramPlugin extends GenericPlugin {

    private $pprPluginSettings;
    private $pprPluginSettingsHandler;

    /**
     * @copydoc Plugin::register()
     */
    function register($category, $path, $mainContextId = null) {
        $success = parent::register($category, $path, $mainContextId);
        $currentContextId = ($mainContextId === null) ? $this->getCurrentContextId() : $mainContextId;
        $this->import('settings.PPRPluginSettings');
        $this->pprPluginSettings = new PPRPluginSettings($currentContextId, $this);
        $this->import('settings.PPRPluginSettingsHandler');
        $this->pprPluginSettingsHandler = new PPRPluginSettingsHandler($this);

        if ($success && $this->getEnabled()) {
            $this->AddSettingsToTemplateManager();
            $this->setupCustomCss();

            $this->import('services.PPRTemplateOverrideService');
            $workflowService = new PPRTemplateOverrideService($this);
            $workflowService->register();

            $this->import('services.PPRWorkflowService');
            $workflowService = new PPRWorkflowService($this);
            $workflowService->register();

            $this->import('services.PPRUserCustomFieldsService');
            $workflowService = new PPRUserCustomFieldsService($this);
            $workflowService->register();

            $this->import('services.PPRSubmissionCustomFieldsService');
            $workflowService = new PPRSubmissionCustomFieldsService($this);
            $workflowService->register();
        }

        return $success;
    }

    function getActions($request, $actionArgs) {
        return $this->pprPluginSettingsHandler->getActions($request, $actionArgs);
    }

    function manage($args, $request) {
        return $this->pprPluginSettingsHandler->manage($args, $request);
    }

    /**
     * @copydoc LazyLoadPlugin::setEnabled()
     */
    function setEnabled($enabled) {
        parent::setEnabled($enabled);
        $this->clearCache();
    }

    /**
     * Clear template/css caches to refresh data when enabling/disabling the plugin and updating its settings
     */
    function clearCache() {
        // CLEAR THE TEMPLATE CACHE TO RELOAD DEFAULT TEMPLATES
        // THIS IS AN ISSUE WITH TEMPLATE OVERRIDE IN PLUGINS
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $templateMgr->clearTemplateCache();
        $templateMgr->clearCssCache();

        $cacheMgr = CacheManager::getManager();
        $cacheMgr->flush();
    }

    /**
     * Add the pprPluginSettings to the template manager to have conditional logic in templates
     */
    function AddSettingsToTemplateManager() {
        $templateMgr = TemplateManager::getManager(Application::get()->getRequest());
        $templateMgr->assign(['pprPluginSettings' => $this->getPluginSettings()]);
    }

    /**
     * Load custom CSS file into all backend pages.
     *
     * @return void
     */
    function setupCustomCss() {
        $request = Application::get()->getRequest();
        $templateMgr = TemplateManager::getManager($request);
        $templateMgr->addStyleSheet(
            'pprOjsPluginCustomCss',
            $request->getBaseUrl() . '/' . $this->getPluginPath() . '/css/iqss.css',
            ['contexts' => array('frontend', 'backend')]
        );
    }

    /**
     * @copydoc Plugin::getDisplayName
     */
    function getDisplayName() {
        return __("plugins.generic.pprPlugin.displayName");
    }

    /**
     * @copydoc Plugin::getDescription
     */
    function getDescription() {
        return __("plugins.generic.pprPlugin.description");
    }

    public function getPluginSettings() {
        return $this->pprPluginSettings;
    }

}

