<?php
namespace DoctrineGui\Service;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use ZF\OAuth2\Doctrine\Entity\Jwt;

class JwtService
{
    /**
     * @var \Doctrine\Common\Persistence\ObjectManager
     * @access protected
     */
    protected $objectManager;

    /**
     * @var \Doctrine\Common\Persistence\ObjectRepository
     */
    protected $jwtRepository;

    /**
     * @param ObjectManager $objectManager
     * @param ObjectRepository $jwtRepository
     */
    public function __construct(
        ObjectManager $objectManager,
        ObjectRepository $jwtRepository
    ) {
        $this->objectManager    = $objectManager;
        $this->jwtRepository    = $jwtRepository;
    }

    /**
     * @param $id
     * @return mixed
     */
    public function find($id)
    {
        return $this->jwtRepository->findOneBy(array('id' => $id));
    }

    /**
     * @param $client_id
     * @return object
     */
    public function findByClientId($client_id)
    {
        return $this->jwtRepository->findOneBy(array('client' => $client_id));
    }

    /**
     * @param Jwt $jwtObject
     * @return Jwt
     * @throws \Exception
     */
    public function update(Jwt $jwtObject)
    {
        try {
            $this->objectManager->persist($jwtObject);
            $this->objectManager->flush();
            return $jwtObject;
        } catch (\Exception $e)
        {
            throw new \Exception($e);
        }
    }

    /**
     * Make sure the jwt client is assigned to the given user
     * This is based on the understanding that the subject of a JWT is a user_id with access to the given jwt object
     * @param $user_id
     * @param $jwt_id
     * @return bool
     */
    public function hasJwtAccess($user_id , $jwt_id)
    {
        $jwtObject = $this->find($jwt_id);

        if (!$jwtObject instanceof Jwt)
        {
            return false;
        }

        $jwt_user_id = $jwtObject->getSubject();

        if ($jwt_user_id == $user_id)
        {
            return true;
        }

        return false;
    }

    /**
     * Remove a given JWT
     * @param $jwt_id
     * @return bool
     * @throws \Exception
     */
    public function delete($jwt_id)
    {
        $JwtObject = $this->find($jwt_id);

        if (!$JwtObject instanceof Jwt)
        {
            return false;
        }

        try {
            $this->objectManager->remove($JwtObject);
            $this->objectManager->flush();
            return true;
        } catch (\Exception $e)
        {
            throw new \Exception($e);
        }

    }


} 