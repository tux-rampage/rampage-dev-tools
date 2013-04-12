<?php
/**
 * This is part of rampage.php
 * Copyright (c) 2012 Axel Helmert
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @category  library
 * @package   rampage.devtools
 * @author    Axel Helmert
 * @copyright Copyright (c) 2013 Axel Helmert
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt GNU General Public License
 */

namespace rampage\devtools\collector;

/**
 * Query profile
 */
class QueryProfile
{
    /**
     * @var string
     */
    protected $sql = '';

    /**
     * @var int
     */
    protected $queryType = 0;

    /**
     * @var float
     */
    protected $startTime = null;

    /**
     * @var float
     */
    protected $endTime = null;

    /**
     * @var array
     */
    protected $parameters = null;

    /**
     * @var array
     */
    protected $callStack = array();

    /**
     * @param string $sql
     * @param int $queryType
     * @param string $parameters
     * @param array $stack
     */
    public function __construct($sql, $queryType, $parameters = null, $stack = array())
    {
        $this->sql = $sql;
        $this->queryType = $queryType;
        $this->parameters = $parameters;
        $this->callStack = $stack;
    }

    /**
     * @return \rampage\devtools\collector\QueryProfile
     */
    public function start()
    {
        $this->startTime = microtime(true);
        return $this;
    }

    /**
     * @return \rampage\devtools\collector\QueryProfile
     */
    public function end()
    {
        $this->endTime = microtime(true);
        return $this;
    }

    /**
     * @return boolean
     */
    public function hasEnded()
    {
        return ($this->endTime !== null);
    }

    /**
     * @return boolean|number
     */
    public function getElapsedTime()
    {
        if (!$this->hasEnded()) {
            return false;
        }

        return $this->endTime - $this->startTime;
    }

    /**
     * @return string
     */
    public function getSql()
    {
        return $this->sql;
    }

    /**
     * @return int
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return int
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * @return int
     */
    public function getQueryType()
    {
        return $this->queryType;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        switch ($this->queryType) {
            case Profiler::SELECT:
                $type = 'SELECT';
                break;
            case Profiler::INSERT:
                $type = 'INSERT';
                break;
            case Profiler::UPDATE:
                $type = 'UPDATE';
                break;
            case Profiler::DELETE:
                $type = 'DELETE';
                break;
            case Profiler::QUERY:
                $type = 'OTHER';
                break;
            case Profiler::CONNECT:
                $type = 'CONNECT';
                break;
        }

        return array(
            'type' => $type,
            'sql' => $this->sql,
            'start' => $this->startTime,
            'end' => $this->endTime,
            'elapsed' => $this->getElapsedTime(),
            'parameters' => $this->parameters,
            'stack' => $this->callStack
        );
    }
}


// Add BjyProfiler alias for ZDT compatibility
class_alias(__NAMESPACE__ . '\QueryProfile', 'BjyProfiler\Db\Profiler\Query');
