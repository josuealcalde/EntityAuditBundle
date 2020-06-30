<?php
/*
 * (c) 2011 SimpleThings GmbH
 *
 * @package SimpleThings\EntityAudit
 * @author Benjamin Eberlei <eberlei@simplethings.de>
 * @link http://www.simplethings.de
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 */

namespace SimpleThings\EntityAudit;

use Doctrine\ORM\Mapping\ClassMetadataInfo;

class AuditConfiguration
{
    private $auditedEntityClasses = array();
    private $globalIgnoreColumns = array();
    private $tablePrefix = '';
    private $tableSuffix = '_audit';
    private $revisionTableName = 'revisions';
    private $revisionFieldName = 'rev';
    private $revisionTypeFieldName = 'revtype';
    private $revisionIdFieldType = 'integer';
    private $usernameCallable;
    private $impersonateCallable;
    private $accesstokenCallable;
    private $ipCallable;
    private $actionCallable;

    /**
     * @param array $classes
     *
     * @return AuditConfiguration
     */
    public static function forEntities(array $classes)
    {
        $conf = new self;
        $conf->auditedEntityClasses = $classes;

        return $conf;
    }

    /**
     * @param ClassMetadataInfo $metadata
     *
     * @return string
     */
    public function getTableName(ClassMetadataInfo $metadata)
    {
        $tableName = $metadata->getTableName();

        //## Fix for doctrine/orm >= 2.5
        if (method_exists($metadata, 'getSchemaName') && $metadata->getSchemaName()) {
            $tableName = $metadata->getSchemaName() . '.' . $tableName;
        }

        return $this->getTablePrefix() . $tableName . $this->getTableSuffix();
    }

    public function getTablePrefix()
    {
        return $this->tablePrefix;
    }

    public function setTablePrefix($prefix)
    {
        $this->tablePrefix = $prefix;
    }

    public function getTableSuffix()
    {
        return $this->tableSuffix;
    }

    public function setTableSuffix($suffix)
    {
        $this->tableSuffix = $suffix;
    }

    public function getRevisionFieldName()
    {
        return $this->revisionFieldName;
    }

    public function setRevisionFieldName($revisionFieldName)
    {
        $this->revisionFieldName = $revisionFieldName;
    }

    public function getRevisionTypeFieldName()
    {
        return $this->revisionTypeFieldName;
    }

    public function setRevisionTypeFieldName($revisionTypeFieldName)
    {
        $this->revisionTypeFieldName = $revisionTypeFieldName;
    }

    public function getRevisionTableName()
    {
        return $this->revisionTableName;
    }

    public function setRevisionTableName($revisionTableName)
    {
        $this->revisionTableName = $revisionTableName;
    }

    public function setAuditedEntityClasses(array $classes)
    {
        $this->auditedEntityClasses = $classes;
    }

    public function getGlobalIgnoreColumns()
    {
        return $this->globalIgnoreColumns;
    }

    public function setGlobalIgnoreColumns(array $columns)
    {
        $this->globalIgnoreColumns = $columns;
    }

    public function createMetadataFactory()
    {
        return new Metadata\MetadataFactory($this->auditedEntityClasses);
    }

    /**
     * @deprecated
     * @param string|null $username
     */
    public function setCurrentUsername($username)
    {
        $this->setUsernameCallable(function () use ($username) {
            return $username;
        });
    }

    /**
     * @return string
     */
    public function getCurrentUsername()
    {
        $callable = $this->usernameCallable;

        return (string) ($callable ? $callable() : "");
    }

    public function setUsernameCallable($usernameCallable)
    {
        // php 5.3 compat
        if (null !== $usernameCallable && !is_callable($usernameCallable)) {
            throw new \InvalidArgumentException(sprintf(
                'Username Callable must be callable. Got: %s',
                is_object($usernameCallable) ? get_class($usernameCallable) : gettype($usernameCallable)
            ));
        }

        $this->usernameCallable = $usernameCallable;
    }

    /**
     * @return callable|null
     */
    public function getUsernameCallable()
    {
        return $this->usernameCallable;
    }

    /**
     * @deprecated
     * @param string|null $impersonate
     */
    public function setCurrentImpersonate($impersonate)
    {
        $this->setImpersonateCallable(function () use ($impersonate) {
            return $impersonate;
        });
    }

    /**
     * @return string
     */
    public function getCurrentImpersonate()
    {
        $callable = $this->impersonateCallable;

        return $callable ? $callable() : null;
    }

    public function setImpersonateCallable($impersonateCallable)
    {
        // php 5.3 compat
        if (null !== $impersonateCallable && !is_callable($impersonateCallable)) {
            throw new \InvalidArgumentException(sprintf(
                'Impersonate Callable must be callable. Got: %s',
                is_object($impersonateCallable) ? get_class($impersonateCallable) : gettype($impersonateCallable)
            ));
        }

        $this->impersonateCallable = $impersonateCallable;
    }

    /**
     * @return callable|null
     */
    public function getImpersonateCallable()
    {
        return $this->impersonateCallable;
    }

    /**
     * @param string|null $accesstoken
     *@deprecated
     */
    public function setCurrentAccesstoken($accesstoken)
    {
        $this->setAccesstokenCallable(function () use ($accesstoken) {
            return $accesstoken;
        });
    }

    /**
     * @return string
     */
    public function getCurrentAccesstoken()
    {
        $callable = $this->accesstokenCallable;

        return $callable ? $callable() : null;
    }

    public function setAccesstokenCallable($accesstokenCallable)
    {
        // php 5.3 compat
        if (null !== $accesstokenCallable && !is_callable($accesstokenCallable)) {
            throw new \InvalidArgumentException(sprintf(
                'Access Token Callable must be callable. Got: %s',
                is_object($accesstokenCallable) ? get_class($accesstokenCallable) : gettype($accesstokenCallable)
            ));
        }

        $this->accesstokenCallable = $accesstokenCallable;
    }

    /**
     * @return callable|null
     */
    public function getAccesstokenCallable()
    {
        return $this->accesstokenCallable;
    }

    /**
     * @deprecated
     * @param string|null $action
     */
    public function setCurrentAction($action)
    {
        $this->setActionCallable(function () use ($action) {
            return $action;
        });
    }

    /**
     * @return string
     */
    public function getCurrentAction()
    {
        $callable = $this->actionCallable;

        return  $callable ? $callable() : null;
    }

    public function setActionCallable($actionCallable)
    {
        // php 5.3 compat
        if (null !== $actionCallable && !is_callable($actionCallable)) {
            throw new \InvalidArgumentException(sprintf(
                'Action Callable must be callable. Got: %s',
                is_object($actionCallable) ? get_class($actionCallable) : gettype($actionCallable)
            ));
        }

        $this->actionCallable = $actionCallable;
    }

    /**
     * @return callable|null
     */
    public function getActionCallable()
    {
        return $this->actionCallable;
    }

    /**
     * @deprecated
     * @param string|null $ip
     */
    public function setCurrentIp($ip)
    {
        $this->setIpCallable(function () use ($ip) {
            return $ip;
        });
    }

    /**
     * @return string
     */
    public function getCurrentIp()
    {
        $callable = $this->ipCallable;

        return $callable ? $callable() : null;
    }

    public function setIpCallable($ipCallable)
    {
        // php 5.3 compat
        if (null !== $ipCallable && !is_callable($ipCallable)) {
            throw new \InvalidArgumentException(sprintf(
                'Ip Callable must be callable. Got: %s',
                is_object($ipCallable) ? get_class($ipCallable) : gettype($ipCallable)
            ));
        }

        $this->ipCallable = $ipCallable;
    }

    /**
     * @return callable|null
     */
    public function getIpCallable()
    {
        return $this->ipCallable;
    }

    public function setRevisionIdFieldType($revisionIdFieldType)
    {
        $this->revisionIdFieldType = $revisionIdFieldType;
    }

    public function getRevisionIdFieldType()
    {
        return $this->revisionIdFieldType;
    }
}
