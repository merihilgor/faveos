<?php

namespace App\Plugins\Ldap\Adldap2\Connections;

use App\Plugins\Ldap\Adldap2\Auth\GuardInterface;
use App\Plugins\Ldap\Adldap2\Schemas\SchemaInterface;
use App\Plugins\Ldap\Adldap2\Configuration\DomainConfiguration;

interface ProviderInterface
{
    /**
      * Constructor.
      *
      * @param DomainConfiguration|array $configuration
      * @param ConnectionInterface|null  $connection
      */
    public function __construct($configuration, ConnectionInterface $connection);

    /**
     * Returns the current connection instance.
     *
     * @return ConnectionInterface
     */
    public function getConnection();

    /**
     * Returns the current configuration instance.
     *
     * @return DomainConfiguration
     */
    public function getConfiguration();

    /**
     * Returns the current Guard instance.
     *
     * @return \Adldap\Auth\Guard
     */
    public function getGuard();

    /**
     * Returns a new default Guard instance.
     *
     * @param ConnectionInterface $connection
     * @param DomainConfiguration $configuration
     *
     * @return \Adldap\Auth\Guard
     */
    public function getDefaultGuard(ConnectionInterface $connection, DomainConfiguration $configuration);

    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection = null);

    /**
     * Sets the current configuration.
     *
     * @param DomainConfiguration|array $configuration
     */
    public function setConfiguration($configuration = []);

    /**
     * Sets the current LDAP attribute schema.
     *
     * @param SchemaInterface|null $schema
     *
     * @return $this
     */
    public function setSchema(SchemaInterface $schema = null);

    /**
     * Returns the current LDAP attribute schema.
     *
     * @return SchemaInterface
     */
    public function getSchema();

    /**
     * Sets the current Guard instance.
     *
     * @param GuardInterface $guard
     *
     * @return $this
     */
    public function setGuard(GuardInterface $guard);

    /**
     * Returns a new Model factory instance.
     *
     * @return \Adldap\Models\Factory
     */
    public function make();

    /**
     * Returns a new Search factory instance.
     *
     * @return \Adldap\Query\Factory
     */
    public function search();

    /**
     * Returns a new Auth Guard instance.
     *
     * @return \Adldap\Auth\Guard
     */
    public function auth();

    /**
     * Connects and Binds to the Domain Controller.
     *
     * If no username or password is specified, then the
     * configured administrator credentials are used.
     *
     * @param string|null $username
     * @param string|null $password
     *
     * @throws \Adldap\Auth\BindException When binding to your LDAP server fails.
     *
     * @return ProviderInterface
     */
    public function connect($username = null, $password = null);
}
