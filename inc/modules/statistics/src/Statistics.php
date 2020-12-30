<?php

namespace Inc\Modules\Statistics\Src;

use Inc\Modules\Statistics\DB;

class Statistics
{
    /**
     * @param null $url
     * @param int $limit
     * @param false $bot
     * @return array
     */
    public function getReferrers($url = null, $limit = 15, $bot = false): array
    {
        $query = $this->db('statistics')
            ->select([
                'referrer',
                'count_unique' => 'COUNT(DISTINCT uniqhash)',
                'count'        => 'COUNT(uniqhash)',
            ])
            ->where('bot', $bot ? 1 : 0)
            ->group(['referrer'])
            ->desc('count');

        if (!empty($url)) {
            $query->where('url', $url);
        }
        if ($limit !== false) {
            $query->limit($limit);
        }

        return $query->toArray();
    }

    /**
     * @param null $referrer
     * @param int $limit
     * @return array
     */
    public function getPages($referrer = null, $limit = 15): array
    {
        $query = $this->db('statistics')
            ->select([
                'url',
                'count_unique' => 'COUNT(DISTINCT uniqhash)',
                'count'        => 'COUNT(uniqhash)',
            ])
            ->group(['url'])
            ->desc('count');

        if ($limit !== false) {
            $query->limit($limit);
        }

        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        return $query->toArray();
    }

    /**
     * @param string $margin
     * @return int
     */
    public function countCurrentOnline($margin = "-5 minutes"): int
    {
        $online = $this->db('statistics')
            ->select([
                'count' => 'COUNT(DISTINCT uniqhash)',
            ])
            ->where('bot', 0)
            ->where('created_at', '>', strtotime($margin))
            ->oneArray();

        return $online['count'];
    }

    /**
     * @param string $date
     * @param int $days
     * @param null $url
     * @param null $referrer
     * @return int
     */
    public function countAllVisits($date = 'TODAY', $days = 1, $url = null, $referrer = null) : int
    {
        $query = $this->db('statistics')
            ->select([
                'count' => 'COUNT(uniqhash)',
            ])
            ->where('bot', 0);

        if ($date != 'ALL') {
            $date = strtotime($date);
            $query->where('created_at', '>=', $date)->where('created_at', '<', $date + $days * 86400);
        }

        if (!empty($url)) {
            $query->where('url', $url);
        }
        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        $all = $query->oneArray();

        return $all['count'];
    }

    /**
     * @param string $date
     * @param int $days
     * @param null $url
     * @param null $referrer
     * @return int
     */
    public function countUniqueVisits($date = 'TODAY', $days = 1, $url = null, $referrer = null) : int
    {
        $query = $this->db('statistics')
            ->select([
                'count' => 'COUNT(DISTINCT uniqhash)',
            ])
            ->where('bot', 0);

        if ($date != 'ALL') {
            $date = strtotime($date);
            $query->where('created_at', '>=', $date)->where('created_at', '<', $date + $days * 86400);
        }

        if (!empty($url)) {
            $query->where('url', $url);
        }
        if (!empty($referrer)) {
            $query->where('referrer', $referrer);
        }

        $record = $query->oneArray();

        return $record['count'];
    }

    /**
     * @param $table
     * @return DB
     */
    protected function db($table)
    {
        return new DB($table);
    }
}
