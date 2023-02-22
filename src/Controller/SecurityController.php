<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class SecurityController extends AbstractController
{
    /**
     * @Route("/registration", name="security_registration")
     */
    public function registration(Request $request, EntityManagerInterface $entityManager, UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $hash = $encoder->encodePassword($user, $user->getPassword());

            $user->setPassword($hash);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('security_login');
        }

        return $this->render('security/registration.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(){
        return $this->render('security/login.html.twig');
    }

    /**
     * @Route("/logout", name="security_logout")
     */
    public function logout(){

    }

    /**
     * @Route("/addpersonnejson/new", name="addpersonnejson")
     */

    public function addpersonnejson(Request $request,UserPasswordEncoderInterface $userPasswordEncoder)
    {

        $em = $this->getDoctrine()->getManager();
        $user= new User();
        $user->setFirstName($request->get('firstName'));
        $user->setPrenom($request->get('lastName'));
        $user->setEmail($request->get('email'));
        $user->setPassword(
            $userPasswordEncoder->encodePassword(
                $user,
                $request->get('password')
            )
        );
        //$personne->setPassword($request->get('password'));

        $user->setRoles("user");


        $em->persist($user);
        $em->flush();

        return new Response("user added");

    }

    /**
     * @Route("/allpersonne", name="allpersonne")
     */
    public function allpersonne(NormalizerInterface $normalizer)
    {
        $repository = $this->getDoctrine()->getRepository(User::class);
        $personne = $repository->findAll();
        $jsonContent = $normalizer->normalize($personne, 'json', ['groups' => 'post:read']);

        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/updatepersonnejson/{id}", name="updatepersonnejson")
     */
    public function updatepersonnejson(Request $request, NormalizerInterface $normalizer, $id,UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $user->setFirstName($request->get('firstName'));
        $user->setPrenom($request->get('lastName'));
        $user->setEmail($request->get('email'));
        $user->setPassword(
            $userPasswordEncoder->encodePassword(
                $user,
                $request->get('password')
            )
        );




        $em->persist($user);
        $em->flush();
        $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'user:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/personneid/{id}", name="personneid")
     */
    public function personneid(Request $request, $id, NormalizerInterface $normalizer)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'user:read']);

        return new Response(json_encode($jsonContent));

    }

    /**
     * @Route("/deletepersonnejson/{id}", name="deletepersonnejson")
     */
    public function deletepersonnejson(Request $request, NormalizerInterface $normalizer, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $em->getRepository(User::class)->find($id);
        $em->remove($user);
        $em->flush();
        $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'user:read']);
        return new Response(json_encode($jsonContent));
    }

    /**
     * @Route("/signinjson", name="signinjson")
     */
    public function signinjson(Request $request,NormalizerInterface $normalizer){
        $email=$request->query->get("email");
        $password=$request->query->get("password");
        $nom=$request->query->get("firstName");
        $prenom=$request->query->get("lastName");

        $em=$this->getDoctrine()->getManager();
        $user=$em->getRepository(User::class)->findOneBy(['email'=>$email]);
        if($user) {
            if (password_verify($password, $user->getPassword())) {
                $jsonContent = $normalizer->normalize($user, 'json', ['groups' => 'user:read']);
                return new Response(json_encode($jsonContent));
            } else {
                return new Response("password not found");
            }
        }else{
            return new Response("user not found");
        }
    }
}
