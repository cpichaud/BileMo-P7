<?php

namespace App\Controller;

use App\Entity\user;
use App\Repository\ClientRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class UserController extends AbstractController
{

    #[Route('/api/users', name: 'user', methods: ['GET'])]
    public function getUserList(UserRepository $userRepository, SerializerInterface $serializer, Request $request): JsonResponse
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 3);

        $userList = $userRepository->findAllUserWithPagination($page, $limit);
        $jsonUserList = $serializer->serialize($userList, 'json', ['groups' => 'user:read']);

        return new JsonResponse($jsonUserList, Response::HTTP_OK, [], true);
    }
    
    #[Route('/api/users/{id}', name: 'detailuser', methods: ['GET'])]
    public function getDetailUser(int $id, SerializerInterface $serializer, UserRepository $userRepository): JsonResponse 
    {
        $user = $userRepository->find($id);
        if ($user) {
            $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'user:read']);
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
   
       $jsonUser = $serializer->serialize($user, 'json', ['groups' => 'getusers']);
   
       $location = $urlGenerator->generate('detailuser', ['id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);
   
       return new JsonResponse($jsonUser, Response::HTTP_CREATED, ["Location" => $location], true);
   }
   
   #[Route('/api/clients/{clientId}/users/{userId}', name: 'deleteUser', methods: ['DELETE'])]
    public function deleteUser(int $clientId, int $userId, UserRepository $userRepository, ClientRepository $clientRepository, EntityManagerInterface $em): Response
    {
        $client = $clientRepository->find($clientId);
        $user = $userRepository->find($userId);

        if (!$client || !$user || $user->getClient() !== $client) {
            return new Response(null, Response::HTTP_NOT_FOUND);
        }

        $em->remove($user);
        $em->flush();

        return new Response(null, Response::HTTP_NO_CONTENT);
    }

}
