<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UsersType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{
    public function getUsers(EntityManagerInterface $em): Response{
        
        //$em = $this->getDoctrine()->getManager();
        $listUsers = $em->getRepository(Users::class)->findBy([],['name' => 'ASC']);
        return $this->render('user/users.html.twig', [
            'listUsers' => $listUsers
        ]);
    }

    public function createUser(Request $request, EntityManagerInterface $em){
        $users = new Users();
        
        $form_users = $this->createForm(UsersType::class, $users);
        $form_users->handleRequest($request);

        if ($form_users->isSubmitted() && $form_users->isValid()){
            // Se realiza la peticion a la base de datos
            $users->setStatus(1);
             
            // tell Doctrine you want to (eventually) save the User (no queries yet)
            $em->persist($users);

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirectToRoute('getUsers');
        }

        return $this->render('user/user_create.html.twig', [
            'form_users' => $form_users->createView()
        ]);
    }
    public function updateUser(Request $request, $id, EntityManagerInterface $em){
        $users = $em->getRepository(Users::class)->find($id);       

        $form_users = $this->createForm(UsersType::class, $users);
        $form_users->handleRequest($request);

        if ($form_users->isSubmitted() && $form_users->isValid()){
            // tell Doctrine you want to (eventually) save the User (no queries yet)
            $em->persist($users);

            // actually executes the queries (i.e. the INSERT query)
            $em->flush();

            return $this->redirectToRoute('getUsers');
        }

        return $this->render('user/user_update.html.twig', [
            'form_users' => $form_users->createView()
        ]);


    }

    public function deleteUser($id, EntityManagerInterface $em){
        $users = $em->getRepository(Users::class)->find($id);       
        $users->setStatus(0);
             
        // tell Doctrine you want to (eventually) save the User (no queries yet)
        $em->persist($users);

        // actually executes the queries (i.e. the INSERT query)
        $em->flush();

        return $this->redirectToRoute('getUsers');

    }

}