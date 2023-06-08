<?php

class CallsBuilder extends Builder
{
    /**
     * Get all calls
     *
     * @return array
     */
    public function buildCalls()
    {
        $query = $this->rule->getSQL('callbacks');
        $handler = $this->rule->getHandler('CallbackHandler');
        $this->sql = $this->container->db->prepare($query);

        return $this->build($handler);
    }

    /**
     * Get all new calls since last run
     *
     * @return array
     */
    public function buildCallsLast()
    {
        $lastSync = DataHelper::getDate($this->container->callsLog);

        $query = $this->rule->getSQL('callbacks_last');
        $handler = $this->rule->getHandler('CallbackHandler');
        $this->sql = $this->container->db->prepare($query);
        $this->sql->bindParam(':lastSync', $lastSync);

        return $this->build($handler);
    }

    /**
     * Get new calls by id
     *
     * @return array
     */
    public function buildCallsById($uidString)
    {
        $query = $this->rule->getSQL('callbacks_uid');
        $handler = $this->rule->getHandler('CallbackHandler');
        $this->sql = $this->container->db->prepare($query);
        $uids = DataHelper::explodeUids($uidString);
        $this->sql->bindParam(':callIds', $uids);

        return $this->build($handler);
    }
}

