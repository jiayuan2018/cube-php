<?php
/**
 *   �������ݿ��
 *
 * @author      huqiu
 */
class MCore_Min_TableIterator
{
    protected $kind;

    function __construct($kind)
    {
        $kind = trim($kind);
        if (empty($kind))
        {
            throw new MCore_Min_DBException('Table kind can not be empty.');
        }
        $this->kind = $kind;
    }

    /**
     * ��ÿ���ֱ�ִ��sql
     */
    public function query($sql, $callback = null, $useSlave = true)
    {
        $tableInfos = $this->getTableInfos($useSlave);

        $list = array();
        foreach ($tableInfos as $tableInfo)
        {
            $dbInfo = $tableInfo->getDBInfo();
            $connection = MCore_Min_DBConection::get($dbInfo);
            $sqlText = str_replace($this->kind, $tableInfo->getTableName(), $sql);
            $ret = $connection->query($sqlText);

            $tableName = $tableInfo->getTableName();
            if ($callback)
            {
                call_user_func($callback, $ret, $tableName);
            }
            else
            {
                $list[$tableName] = $ret;
            }
        }
        return $list;
    }

    protected function getTableInfos($useSlave)
    {
        $deployData = MCore_Min_TableConfig::getDeployData();
        return MCore_Min_TableConfig::getTableInfos($deployData, $this->kind, $useSlave);
    }
}
?>
