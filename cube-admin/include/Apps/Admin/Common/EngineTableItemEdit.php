<?php
class MApps_Admin_Common_EngineTableItemEdit extends MApps_AdminPageBase
{
    private $controller;

    protected function main()
    {
        $this->controller = new MAdmin_Views_DataTableEditController(array(), MEngine_EngineDB::create());
        $this->controller->render();
    }

    protected function outputBody()
    {
        $this->controller->output();
    }
}