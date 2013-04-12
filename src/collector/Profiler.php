<?php
/**
 * LICENSE: $license_text$
 *
 * @author    Axel Helmert <ah@luka.de>
 * @copyright Copyright (c) 2012 LUKA netconsult GmbH (www.luka.de)
 * @license   $license$
 * @version   $Id$
 */

namespace rampage\devtools\collector;

use Zend\Db\Adapter\Profiler\ProfilerInterface;
use Zend\Db\Adapter\StatementContainerInterface;

/**
 * Database Profiler
 */
class Profiler implements ProfilerInterface
{
    /**
     * Logical OR these together to get a proper query type filter
     */
    const CONNECT = 1;
    const QUERY = 2;
    const INSERT = 4;
    const UPDATE = 8;
    const DELETE = 16;
    const SELECT = 32;
    const TRANSACTION = 64;

    /**
     * @var array
     */
    protected $profiles = array();

    /**
     * @var boolean
    */
    protected $enabled = true;

    /**
     * @var int
     */
    protected $filterTypes = null;

    /**
     * @param string $enabled
     */
    public function __construct($enabled = true)
    {
        $this->enabled = $enabled;
    }

    /**
     * @return \rampage\devtools\collector\Profiler
     */
    public function enable()
    {
        $this->enabled = true;
        return $this;
    }

    /**
     * @return \rampage\devtools\collector\Profiler
     */
    public function disable()
    {
        $this->enabled = false;
        return $this;
    }

    /**
     * @param string $queryTypes
     * @return \rampage\devtools\collector\Profiler
     */
    public function setFilterQueryType($queryTypes = null)
    {
        $this->filterTypes = $queryTypes;
        return $this;
    }

    /**
     * @return number
     */
    public function getFilterQueryType()
    {
        return $this->filterTypes;
    }

    /**
     * @param string $sql
     * @param string $parameters
     * @param string $stack
     * @return NULL|mixed
     */
    public function startQuery($sql, $parameters = null, $stack = null)
    {
        if (!$this->enabled) {
            return null;
        }

        if (is_null($stack)) {
            if (version_compare('5.3.6', phpversion(), '<=')) {
                $stack = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            } else {
                $stack = array();
            }
        }

        // try to detect the query type
        switch (strtolower(substr(ltrim($sql), 0, 6))) {
            case 'select':
                $queryType = static::SELECT;
                break;
            case 'insert':
                $queryType = static::INSERT;
                break;
            case 'update':
                $queryType = static::UPDATE;
                break;
            case 'delete':
                $queryType = static::DELETE;
                break;
            default:
                $queryType = static::QUERY;
                break;
        }

        $profile = new QueryProfile($sql, $queryType, $parameters, $stack);
        $this->profiles[] = $profile;

        return $profile->start();
    }

    /**
     * @return boolean
     */
    public function endQuery()
    {
        if (!$this->enabled) {
            return false;
        }

        $query = end($this->profiles);
        if ($query) {
            $query->end();
        }

        return true;
    }

    /**
     * @param string $queryTypes
     * @return array
     */
    public function getQueryProfiles($queryTypes = null)
    {
        if (($queryTypes === null) && !$this->filterTypes) {
            return $this->profiles;
        }

        $profiles = array();

        if ($queryTypes === null) {
            $queryTypes = $this->filterTypes;
        }

        foreach ($this->profiles as $id => $profile) {
            if ($profile->getQueryType() & $queryTypes) {
                $profiles[$id] = $profile;
            }
        }

        return $profiles;
    }

    /**
     * @see \Zend\Db\Adapter\Profiler\ProfilerInterface::profilerStart()
     */
    public function profilerStart($target)
    {
        if ($target instanceof StatementContainerInterface) {
            $sql = $target->getSql();
            $params = $target->getParameterContainer();
        } else {
            $sql = $target;
            $params = null;
        }

        $this->startQuery($sql, $params);
    }

    /**
     * @see \Zend\Db\Adapter\Profiler\ProfilerInterface::profilerFinish()
     */
    public function profilerFinish()
    {
        $this->endQuery();
    }
}


// Add BjyProfiler alias for ZDT compatibility
class_alias(__NAMESPACE__ . '\Profiler', 'BjyProfiler\Db\Profiler\Profiler');
