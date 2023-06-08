<?php


class SubscribersBuilder extends Builder
{
    /**
     * Get all subscribers
     *
     * @return array
     */
    public function buildSubscribers()
    {
        $query = $this->rule->getSQL('subscribers');
        $handler = $this->rule->getHandler('SubscriberHandler');
        $this->sql = $this->container->db->prepare($query);

        return $this->build($handler);
    }
    
    /**
     * Get all new subscribers since last run
     *
     * @return array
     */
    public function buildSubscribersLast()
    {
        $lastSync = DataHelper::getDate($this->container->subscriberLog);

        $query = $this->rule->getSQL('subscribers_last');
        $handler = $this->rule->getHandler('SubscriberHandler');
        $this->sql = $this->container->db->prepare($query);
        $this->sql->bindParam(':lastSync', $lastSync);

        return $this->build($handler);
    }
    
    /**
     * Get new subscribers by id
     *
     * @return array
     */
    public function buildSubscribersById($uidString)
    {
        $query = $this->rule->getSQL('subscribers_uid');
        $handler = $this->rule->getHandler('SubscriberHandler');
        $this->sql = $this->container->db->prepare($query);
        $uids = DataHelper::explodeUids($uidString);
        $this->sql->bindParam(':subscriberIds', $uids);

        return $this->build($handler);
    }
}
