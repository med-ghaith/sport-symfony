<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\ExerciceRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/exercice")
 */
class ExerciceController extends AbstractController
{
    /**
     * @Route("/", name="exercice_index1", methods={"GET"})
     */
    public function index(ExerciceRepository $exerciceRepository): Response
    {
//        dump($exerciceRepository->findByEquipments("Pull-up bar"));
        return $this->render('exercice/index.html.twig', [
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/all", name="exercice_index1", methods={"GET"})
     */
    public function getJsonExercices(ExerciceRepository $exerciceRepository): Response
    {
        return $this->json($exerciceRepository->findAll(),200,[],['groups'=>'exercise:read']);
    }


    /**
     * @Route("/generated", name="exercice_generated", methods={"GET", "POST"})
     * @param Request $request
     * @param ExerciceRepository $exerciceRepository
     * @return Response
     */
    public function generatedProgram(Request $request, ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('exercice/index.html.twig', [
            'exercices' => $exerciceRepository->findByEquipments($request->query->get('equip'),$request->query->get('equip1'),$request->query->get('equip2')),
        ]);
    }

    /**
     * @Route("/", name="exercice_back_index", methods={"GET"})
     */
    public function index_back(ExerciceRepository $exerciceRepository): Response
    {
        return $this->render('back-office/back_office_exercice/index.html.twig', [
            'exercices' => $exerciceRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="exercice_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $exercice = new Exercice();
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form['imageUrl']->getData();
            //$file = $request->files->get('post')['imageUrl'];

            $upload_directory = $this->getParameter('upload_directory');

            $fileName = md5(uniqid()) . '.' . $file->guessExtension();

            $file->move(
                $upload_directory,
                $fileName
            );

            $exercice->setImageUrl("http://localhost:8000/uploads/image/" . $fileName);

            $entityManager->persist($exercice);
            $entityManager->flush();

            return $this->redirectToRoute('exercice_back_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice/new.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exercice_show", methods={"GET"})
     */
    public function show(Exercice $exercice): Response
    {
        return $this->render('exercice/show.html.twig', [
            'exercice' => $exercice,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="exercice_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Exercice $exercice, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ExerciceType::class, $exercice);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('exercice_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('exercice/edit.html.twig', [
            'exercice' => $exercice,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="exercice_delete", methods={"POST"})
     */
    public function delete(Request $request, Exercice $exercice, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $exercice->getId(), $request->request->get('_token'))) {
            $entityManager->remove($exercice);
            $entityManager->flush();
        }

        return $this->redirectToRoute('back_office_exercice', [], Response::HTTP_SEE_OTHER);
    }


}
