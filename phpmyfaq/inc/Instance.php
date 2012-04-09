<?php
/**
 * The main phpMyFAQ instances class
 *
 * PHP Version 5.3
 *
 * This Source Code Form is subject to the terms of the Mozilla Public License,
 * v. 2.0. If a copy of the MPL was not distributed with this file, You can
 * obtain one at http://mozilla.org/MPL/2.0/.
 *
 * @category  phpMyFAQ
 * @package   PMF_Instance
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2012 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      http://www.phpmyfaq.de
 * @since     2012-02-20
 */

if (!defined('IS_VALID_PHPMYFAQ')) {
    exit();
}

/**
 * PMF_Instance
 *
 * @category  phpMyFAQ
 * @package   PMF_Instance
 * @author    Thorsten Rinne <thorsten@phpmyfaq.de>
 * @copyright 2012 phpMyFAQ Team
 * @license   http://www.mozilla.org/MPL/2.0/ Mozilla Public License Version 2.0
 * @link      http://www.phpmyfaq.de
 * @since     2012-02-20
 */
class PMF_Instance
{
    /**
     * Configuration
     *
     * @var PMF_Configuration
     */
    protected $_config = null;

    /**
     * Instance ID
     *
     * @var integer
     */
    protected $_id;

    /**
     * Constructor
     *
     * @return PMF_Instance
     */
    public function __construct(PMF_Configuration $config)
    {
        $this->_config = $config;
    }

    /**
     * Adds a new instance
     *
     * @param array $data
     *
     * @return integer $id
     */
    public function addInstance(Array $data)
    {
        $this->setId($this->_config->getDb()->nextId(SQLPREFIX . 'faqinstances', 'id'));

        $insert = sprintf(
            "INSERT INTO %sfaqinstances VALUES (%d, '%s', '%s', '%s', NOW(), NOW())",
            SQLPREFIX,
            $this->getId(),
            $data['url'],
            $data['instance'],
            $data['comment']
        );

        if (! $this->_config->getDb()->query($insert)) {
            return 0;
        }

        return $this->getId();
    }

    /**
     * Sets the instance ID
     *
     * @param int $id
     */
    public function setId($id)
    {
        $this->_id = (int)$id;
    }

    /**
     * Returns the current instance id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }

    /**
     * Returns all instances
     *
     * @return array
     */
    public function getAllInstances()
    {
        $select = sprintf(
            "SELECT * FROM %sfaqinstances",
            SQLPREFIX
        );

        $result = $this->_config->getDb()->query($select);

        return $this->_config->getDb()->fetchAll($result);
    }

    /**
     * Returns the instance
     *
     * @param integer $id
     *
     * @return array
     */
    public function getInstanceById($id)
    {
        $select = sprintf(
            "SELECT * FROM %sfaqinstances WHERE id = %d",
            SQLPREFIX,
            (int)$id
        );

        $result = $this->_config->getDb()->query($select);

        return $this->_config->getDb()->fetchAll($result);
    }

    /**
     * Returns the instance
     *
     * @param string $url
     *
     * @return array
     */
    public function getInstanceByUrl($url)
    {
        $select = sprintf(
            "SELECT * FROM %sfaqinstances WHERE url = '%s'",
            SQLPREFIX,
            $url
        );

        $result = $this->_config->getDb()->query($select);

        return $this->_config->getDb()->fetchAll($result);
    }

    /**
     * Deletes an instance
     *
     * @return boolean
     */
    public function removeInstance($id)
    {
        $deletes = array(
            sprintf(
                "DELETE FROM %sfaqinstances WHERE id = %d",
                SQLPREFIX,
                (int)$id
            ),
            sprintf(
                "DELETE FROM %sfaqinstances_config WHERE instance_id = %d",
                SQLPREFIX,
                (int)$id
            ),
        );

        foreach ($deletes as $delete) {
            $success = $this->_config->getDb()->query($delete);
            if (! $success) {
                return false;
            }
        }

        return true;
    }

    /**
     * Adds a configuration item for the database
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return boolean
     */
    public function addConfig($name, $value)
    {
        $insert = sprintf(
            "INSERT INTO
                %sfaqinstances_config
            VALUES
                (%d, '%s', '%s')",
            SQLPREFIX,
            $this->getId(),
            $this->_config->getDb()->escape(trim($name)),
            $this->_config->getDb()->escape(trim($value))
        );

        return $this->_config->getDb()->query($insert);
    }
}