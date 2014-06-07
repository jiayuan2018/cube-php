<?php
class MAdmin_Views_ListHeader
{
    private $noSortFields = array();
    private $thisValueFields = array();
    private $onlyDisplayFields = array();
    private $hideFields = array();
    private $fieldOrder = array();

    // if is not set, will be the key
    private $names = array();

    // if is not set, will be 'center', you can set '' to make it will not be set to 'center'
    private $align = array();

    public function __construct($conf)
    {
        $keys = array(
            'noSortFields' => 'no_sort_filds',
            'thisValueFields' => 'this_value_fields',
            'onlyDisplayFields' => 'only_display_fields',
            'hideFields' => 'hide_fields',
            'names' => 'names',
            'align' => 'align',
            'order' => 'order',
        );
        foreach ($keys as $key => $conf_key)
        {
            if (isset($conf[$conf_key]) && is_array($conf[$conf_key]))
            {
                $this->$key = $conf[$conf_key];
            }
        }
    }

    public function getHeaderData($input, $tableFields)
    {
        $list = array();

        // add reverse url / this vaule url
        foreach ($tableFields as $key)
        {
            $info = array();

            // 表头逆序排列
            if (!in_array($key, $this->noSortFields))
            {
                $queryInfo = $input->getPageIdentityData();
                $queryInfo['pageinfo_order'] = $input['pageinfo_order_reverse'];
                $queryInfo['pageinfo_sortby'] = $key;
                $info['url_reverse_order'] = MCore_Str_Url::buildUrl($queryInfo);
            }

            // 值过滤
            if (in_array($key,$this->thisValueFields))
            {
                $queryInfo = $input->getPageIdentityData();
                unset($queryInfo[$key]);
                $info['url_this_value'] = MCore_Str_Url::buildUrl($queryInfo);
            }

            // name
            $info['name'] = isset($this->names[$key]) ? $this->names[$key] : $key;
            $list[$key] = $info;
        }

        foreach ($this->names as $key => $name)
        {
            if (!isset($list[$key]))
            {
                $list[$key] = array('name' => $name);
            }
        }

        // align
        foreach ($list as $key => $item)
        {
            // align
            if (isset($this->align[$key]))
            {
                $item['align'] = $this->align[$key];
            }
            else
            {
                $item['align'] = 'center';
            }
            $list[$key] = $item;
        }

        // remove hide
        if (!empty($this->hideFields))
        {
            $list = MCore_Tool_Array::remove($list, $this->hideFields);
        }

        // only display what should be displayed.
        if (!empty($this->onlyDisplayFields))
        {
            $list = MCore_Tool_Array::fetch($list, $this-onlyDisplayFields);
        }
        return $list;
    }

}
