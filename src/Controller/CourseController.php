<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Gym;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use App\Repository\GymRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Id;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\Length;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

/**
 * @Route("/course")
 */
class CourseController extends AbstractController
{
    /**
     * @Route("/{id}/liste", name="coursegym", methods={"GET"})
     */
    public function showcoursegym(CourseRepository $courseRepository,$id,Request $request,PaginatorInterface $paginator): Response
    {  
        $course=$courseRepository->listcoursebygym($id);
        // Paginate the results of the query
        $appointments = $paginator->paginate(
            // Doctrine Query, not results
            $course,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        if(count($course)==0){
            return $this->redirectToRoute('course_new', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }
        return $this->render('course/index.html.twig', [
            'courses'=>$appointments,'id'=>$id
        ]);       
       
    }
    /**
     * @Route("/", name="courseallgym", methods={"GET"})
     */
    public function showAll(CourseRepository $courseRepository,Request $request,PaginatorInterface $paginator): Response
    {  
        $course=$courseRepository->findAll();
        // Paginate the results of the query
        $appointments = $paginator->paginate(
            // Doctrine Query, not results
            $course,
            // Define the page parameter
            $request->query->getInt('page', 1),
            // Items per page
            4
        );
        return $this->render('course/index.html.twig', [
            'courses'=>$appointments,'id'=>0
        ]);       
       
    }
    /**
     * @Route("/{id}/new", name="course_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager,$id): Response
    {
        $course = new Course();
        $gym=$this->getDoctrine()->getRepository(Gym::class)->find($id);
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
           {/*
            dd($course);
            /** @var UploadedFile $file 
            $file = ($request->files->get('course')['video']);
            if ($file) {
                $newfilename = md5(uniqid()) . '.' . $file->guessClientExtension();

                $file->move(
                    $this->getParameter('upload_directory'),
                    $newfilename
                );
                $course->setVideo($newfilename);
            }
        */}
            $course->setGym($gym);
            $entityManager->persist($course);
            $entityManager->flush();
            return $this->redirectToRoute('coursegym', ['id'=>$id], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
            'id'=>$id,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="course_edit", methods={"GET", "POST"})
     */
    public function edit(GymRepository $gymeRepository,Request $request, Course $course, EntityManagerInterface $entityManager,$id): Response
    {
        $c=$gymeRepository->getGymbyCourse($id);
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('coursegym', ['id'=>$c[0]->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'id'=>$c[0]->getId(),
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/delete", name="course_delete")
     */
    public function delete(GymRepository $gymeRepository, Course $course,$id): Response
    {
        $c=$gymeRepository->getGymbyCourse($id);
        $em=$this->getDoctrine()->getRepository(Course::class);
        $course=$em->find($id);//pour avoir tout l'objet
        $entityManager=$this->getDoctrine()->getManager();
        $entityManager->remove($course);
        $entityManager->flush();
        if(count($c)==0){
            return $this->redirectToRoute('gym_show',['id'=>$id]);
        }else{
            return $this->redirectToRoute('coursegym', ['id'=>$c[0]->getId()], Response::HTTP_SEE_OTHER);
        }
        
    }   
}
