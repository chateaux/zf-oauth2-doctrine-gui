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

class ClientService
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
     * @param ObjectRepository $clientRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        ObjectRepository $clientRepository
    ) {
        $this->objectManager    = $objectManager;
        $this->clientRepository = $clientRepository;
    }

    /**
     * Find client by its primary key
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->clientRepository->findOneBy(array('id' => $id));
    }

    /**
     * Return all the clients
     * @return array
     */
    public function findAll()
    {
        return $this->clientRepository->findAll();
    }

    /**
     * Find by the associated user id
     * @param $user_id
     * @return object
     */
    public function findByUserId($user_id)
    {
        return $this->clientRepository->findBy(array('user' => $user_id));
    }

    /**
     * Find by the client_id (name i.e. test_client)
     * @param $cient_id
     * @return mixed
     */
    public function findByClientId($cient_id)
    {
        return $this->clientRepository->findOneBy(array('clientId' => $cient_id));
    }

    /**
     * Persist a client
     * @param Client $clientObject
     * @return Client
     * @throws \Exception
     */
    public function update(Client $clientObject)
    {
        try {
            $this->objectManager->persist($clientObject);
            $this->objectManager->flush();
            return $clientObject;
        } catch (\Exception $e)
        {
            throw new \Exception($e);
        }
    }

    /**
     * @param $id
     * @return bool
     * @throws \Exception
     */
    public function delete($id)
    {
        $clientObject = $this->find($id);

        if (!$clientObject instanceof Client)
        {
            return false;
        }

        try {
            $this->objectManager->remove($clientObject);
            $this->objectManager->flush();
            return true;
        } catch (\Exception $e)
        {
            throw new \Exception($e);
        }

    }

    /**
     * Use this in your controller to test whether or not a given user id is allowed to manage a
     * given client.
     * @param  $user_id
     * @param  $client_id
     * @return bool
     */
    public function hasClientAccess($user_id , $client_id)
    {
        $clientArray = $this->findByUserId($user_id);

        foreach ($clientArray AS $clientObject)
        {
            if ($clientObject->getId() == $client_id)
            {
                return true;
            }
        }

        return false;

    }

} 