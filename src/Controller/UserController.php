<?php

namespace App\Controller;

use App\Entity\user;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use JMS\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use JMS\Serializer\SerializationContext;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Contracts\Cache\TagAwareCacheInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;


class UserController extends AbstractController
{

    #[Route('/api/users', name: 'users', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer, Request $request, TagAwareCacheInterface $cachePool): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $idCache = "getAllUsers-" . $page . "-" . $limit;

        $jsonUserList = $cachePool->get($idCache, function (ItemInterface $item) use ($userRepository, $page, $limit, $serializer) {
            $item->tag("usersCache");
            $userList = $userRepository->findAllUserWithPagination($page, $limit);
            $context = SerializationContext::create()->setGroups(["user:read"]);
            return  $serializer->serialize($userList, 'json', $context);
        });

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/users/{id}', name: 'detailuser', methods: ['GET'])]
    public function getDetailUser(int $id, SerializerInterface $serializer, UserRepository $userRepository): JsonResponse 
    {
        $user = $userRepository->find($id);
        if ($user) {
            $context = SerializationContext::create()->setGroups(["user:read"]);
            $jsonUser = $serializer->serialize($user, 'json', $context);
            return new JsonResponse($jsonUser, Response::HTTP_OK, [], true);
        }
        return new JsonResponse(null, Response::HTTP_NOT_FOUND);
    }
    

   #[Route('/api/users/create', name: 'createtUser', methods: ['POST'])]
   public function createUser(Request $request, SerializerInterface $serializer, UserRepository $userRepository, EntityManagerInterface $em, UrlGeneratorInterface $urlGenerator, ClientRepository $clientRepository, UserPasswordHasherInterface $passwordHasher): JsonResponse 
   {
       $user = $serializer->deserialize($request->getContent(), User::class, 'json');
   
       $content = $request->toArray();
   
       $password = $passwordHasher->hashPassword($user, $content['password']);
       $user->setPassword($password);
   
       $idClient = $content['idClient'] ?? -1;
   
       $client = $clientRepository->find($idClient);
       if (!$client) {
           return new JsonResponse('No client found with the provided ID', Response::HTTP_BAD_REQUEST);
       }
   
       $user->setClient($client);
       $em->persist($user);
       $em->flush();
   
       $context = SerializationContext::create()->setGroups(["user:read"]);
       $jsonUser = $serializer->serialize($user, 'json', $context);
   
       $location = $urlGenerator->generate('detailuser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
   
       return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
   }
   
   #[Route('/api/clients/{clientId}/users/{userId}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(int $clientId, int $userId, UserRepository $userRepository, ClientRepository $clientRepository, EntityManagerInterface $em, TagAwareCacheInterface $cachePool): Response
    {
        $client = $clientRepository->find($clientId);
        $user = $userRepository->find($userId);

        if (!$client || !$user || $user->getClient() !== $client) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $cachePool->invalidateTags(["usersCache"]);
        $em->remove($user);
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }
}