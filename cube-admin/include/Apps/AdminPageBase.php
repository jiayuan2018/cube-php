<?php
/**
 * Basic Page for Admin
 *
 * @author huqiu
 */
abstract class MApps_AdminPageBase extends MCore_Web_BasePageApp
{
    protected $userData;

    protected $host;
    protected $moduleMan;

    private $beginOutput = false;

    protected function checkAuth()
    {
        if (!MAdmin_Init::checkInit())
        {
            $this->go2('/init');
        }
        $userData = MAdmin_UserAuth::checkLoginByGetUser();
        if (!$userData)
        {
            $this->go2('/admin/user/login');
        }
        $this->userData = $userData;
        $this->moduleMan = new MAdmin_Module($this->request->getPath(), $userData);
        if (!$this->moduleMan->userHasAuth())
        {
            $this->go2('/admin');
        }
    }

    protected function outputHttp()
    {
        header('Content-type: text/html; charset=utf-8');
        header("Cache-Control: no-cache");
    }

    protected function init()
    {
        $this->getResTool()->addCss('admin/admin-base.css');
        $this->getResTool()->addFootJs('admin/AAdminGlobal.js');
    }

    protected function getTitle()
    {
        $module = $this->moduleMan->getCurrentModuleInfo();
        $title = '';
        if ($module)
        {
            $title = $module['name'];
        }
        return $title . ' - Cube for ' . APP_NAME;
    }

    public static function createDisplayView()
    {
        $viewDisplyer = new MCore_Web_SimpleView(CUBE_DEV_ROOT_DIR . '/template');
        $view = new MCore_Web_View($viewDisplyer);
        $baseData = array();
        $baseData['static_pre_path'] = 'http://' . MCore_Tool_Conf::getDataConfigByEnv('mix', 'static_res_host') . '/cube-admin-mix';
        $view->setBaseData('base_data', $baseData);
        return $view;
    }

    protected function createView()
    {
        return self::createDisplayView();
    }

    protected function output()
    {
        $this->beginOutput = true;
        $this->outputHttp();
        $this->outputHead();
        $this->outputBody();
        $this->outputTail();
    }

    protected function outputHead()
    {
        $header_data = array();
        $header_data['css_html'] = $this->getResTool()->getCssHtml();
        $header_data['js_html'] = $this->getResTool()->getHeadJsHtml();

        if ($this->userData)
        {
            $header_data['proxy_auth'] = MAdmin_UserAuth::hasAuthProxy();
            $header_data['right_links'] = MAdmin_UserAuth::getRightLinks();
            $header_data['user_data'] = $this->userData->getData();
            $header_data['title'] = $this->getTitle();

            $header_data['module_list'] = $this->moduleMan->getModuleList();
            $header_data['module_info'] = $this->moduleMan->getCurrentModuleInfo();
            $header_data['base_path'] = $this->moduleMan->getBasePath() . DS;
        }

        $this->getView()->setData('header_data', $header_data);
        $this->getView()->display('admin/base/head.html');
    }

    protected function outputTail()
    {
        $tailData = array();
        $tailData['js_html'] = $this->getResTool()->getTailJsHtml();
        $this->getView()->setData('tail_data', $tailData)->display('admin/base/tail.html');
    }

    protected function outputBody()
    {
    }

    protected function processException($ex)
    {
        if (!$this->beginOutput)
        {
            $this->outputHttp();
            $this->outputHead();
        }
        $page_data = array();
        $page_data['msg'] = $ex->getMessage();
        if (!MCore_Tool_Env::isProd())
        {
            $page_data['trace_str'] = $ex->getTraceAsString();
            $page_data['trace'] = var_export($ex->getTrace(), true);
        }
        $this->getView()->setPageData($page_data)->display('admin/base/error.html');

        if (!$this->beginOutput)
        {
            $this->outputTail();
        }
    }
}
