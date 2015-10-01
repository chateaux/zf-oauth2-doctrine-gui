<?php
namespace DoctrineGui\Service;

/**
 * @author Brendan <b.nash at southeaster dot com>
 *
 * @Contributors:
 *
 */

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use ZF\OAuth2\Doctrine\Entity\Client;
use ZF\OAuth2\Doctrine\Entity\Scope;

class ScopeService
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     * @access protected
     */
    protected $objectManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $clientRepository;

    /**
     * @param ObjectManager $objectManager
     * @param ObjectRepository $scopeRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        ObjectRepository $scopeRepository
    ) {
        $this->objectManager    = $objectManager;
        $this->scopeRepository  = $scopeRepository;
    }

    /**
     * Returns a specific scope based on the primary key
     * @param $id
     * @return object
     */
    public function find($id)
    {
        return $this->scopeRepository->find($id);
    }

    /**
     * @param $scope_name
     * @return object
     */
    public function findByName($scope_name)
    {
        return $this->scopeRepository->findOneBy(array('scope' => $scope_name));
    }

    /**
     * Returns a list of all scopes
     * @return array
     */
    public function findAll()
    {
        return $this->scopeRepository->findAll();
    }

    /**
     * Remove a clients scopes
     * @param Client $clientObject
     */
    public function removeScopes(Client $clientObject)
    {
        $scopeObjectArray = $this->findAll();

        foreach ($scopeObjectArray AS $scopeObject)
        {
            if ( $scopeObject instanceof Scope )
            {
                $scopeObject->removeClient($clientObject);
                $this->update($scopeObject);
            }
        }

    }

    /**
     * @param Scope $scopeObject
     * @return mixed
     * @throws \Exception
     */
    public function update(Scope $scopeObject)
    {
        try {
            $this->objectManager->persist($scopeObject);
            $this->objectManager->flush();
            return $scopeObject;
        } catch (\Exception $e)
        {
            throw new \Exception($e);
        }

    }

    /**
     * @param $scope_id
     * @return bool
     * @throws \Exception
     */
    public function delete($scope_id)
    {
        $scopeObject = $this->find($scope_id);

        if (!$scopeObject instanceof Scope)
        {
            return false;
        }

        try {
            $this->objectManager->remove($scopeObject);
            $this->objectManager->flush();
            return true;
        } catch (\Exception $e)
        {
            throw new \Exception($e);
        }

    }

    /**
     * @param $scope_id
     * @return bool
     */
    public function toggle($scope_id)
    {
        $scopeObject = $this->find($scope_id);

        if (!$scopeObject instanceof Scope)
        {
            return false;
        }

        $scopeObject->setIsDefault(!$scopeObject->getIsDefault());

        $this->update($scopeObject);
    }

} 