<?php
class MApps_Admin_User_UserEdit extends MApps_AdminPageBase implements MAdmin_Views_OnItemAction
{
    private $auth_keys;

    protected function main()
    {
        $this->auth_keys = $this->moduleMan->getModuleAuthKeys();

        $input_keys = array('email', 'pwd', 'is_sysadmin', 'app_admin_key');

        $keys = $this->auth_keys;
        foreach ($keys as $key)
        {
            $input_keys[] = 'input_auth_' . $key;
        }
        $identity_keys = array('uid');
        $controller = new MAdmin_Views_ItemActionController($identity_keys, $input_keys, $this);
        $controller->dispatch();
    }

    public function onDelete($identity_info)
    {
        MAdmin_UserRaw::delete($identity_info['uid']);
    }

    public function onSubmit($identity_info, $input_info)
    {
        $uid = $identity_info['uid'];
        $auth_keys = array();

        $keys = $this->auth_keys;
        foreach ($keys as $key)
        {
            if (!empty($input_info['input_auth_' . $key]))
            {
                $auth_keys[] = $key;
            }
        }

        $app_admin_key = $input_info['app_admin_key'];
        $is_sysadmin = $input_info['is_sysadmin'];

        if ($uid)
        {
            $info = array();
            $info['is_sysadmin'] = $is_sysadmin;
            $info['app_admin_key'] = $app_admin_key;
            MAdmin_UserRaw::updateInfo($uid, $info, $auth_keys);
        }
        else
        {
            MAdmin_UserRaw::create($input_info['email'], $input_info['pwd'], $auth_keys, $is_sysadmin, $app_admin_key);
        }
    }

    public function onEdit($identity_info)
    {
        $data = array();
        $auth_keys = array();
        $uid = $identity_info['uid'];
        if ($uid)
        {
            $user_info = MAdmin_UserRaw::getInfo($uid);
            $auth_keys = $user_info['auth_keys'];
            $data['email'] = $user_info['email'];
            $data['app_admin_key'] = $user_info['app_admin_key'];
            $data['is_sysadmin_checked'] = $user_info['is_sysadmin'] ? 'checked=true' : '';
        }

        $auth_infos = array();
        $keys = $this->auth_keys;
        foreach ($keys as $key)
        {
            $info = array();
            $info['checked'] = in_array($key, $auth_keys) ? 'checked=true' : '';
            $info['name'] = 'input_auth_' . $key;
            $auth_infos[$key] = $info;
        }

        $data['uid'] = $uid;
        $data['auth_infos'] = $auth_infos;
        $this->getView()->setPageData($data);
    }

    protected function outputBody()
    {
        $this->getView()->display('admin/user-edit.html');
    }
}
