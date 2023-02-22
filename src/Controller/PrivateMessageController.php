<?php

namespace App\Controller;

use App\Entity\PrivateMessage;
use App\Entity\User;
use App\Form\PrivateMessageType;
use App\Repository\PrivateMessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use phpDocumentor\Reflection\Types\This;
use PhpParser\Node\Expr\Array_;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;


/**
 * @Route("/private/message")
 */
class PrivateMessageController extends AbstractController
{

    /**
     * @Route("/", name="private_message_index", methods={"GET"})
     */
    public function index(EntityManagerInterface $entityManager): Response
    {

        $privateMessages = $entityManager
            ->getRepository(PrivateMessage::class)
            ->findAll();

        $eliB3atelhom = array();
        $eliBa3thouli = array();

        $conversations = $entityManager
            ->getRepository(PrivateMessage::class)->showAllUserWithJaw($this->getUser()->getId());

        foreach ($conversations as $c) {
            if ($this->getUser()->getId() == $c->getIdFirstUser()) {
                if (!in_array($c->getIdSecondUser(), $eliB3atelhom)) {
                    $eliB3atelhom[] = $c->getIdSecondUser();
                }
            } elseif ($this->getUser()->getId() == $c->getIdSecondUser()) {
                if (!in_array($c->getIdFirstUser(), $eliBa3thouli)) {
                    $eliBa3thouli[] = $c->getIdFirstUser();
                }
            }
        }

        $merg = array_unique(array_merge($eliB3atelhom, $eliBa3thouli), SORT_REGULAR);

        $ussrs = $entityManager
            ->getRepository(User::class)
            ->findBy(array('id' => $merg));


        $messages = $entityManager
            ->getRepository(PrivateMessage::class)->findAllMessagesBetweenTwoUsers2($this->getUser()->getId(), $entityManager->getRepository(User::class)->find(34));


        $testCol = array();
        //$lastMsg = string;
        foreach ($ussrs as $us) {
            $pvmsg = $entityManager
                ->getRepository(PrivateMessage::class)->findOneByIdUser($us->getId())[0];
            if ($pvmsg->getIdFirstUser() == $us->getId()) {
                $lastMsg = $entityManager->getRepository(User::class)->find($pvmsg->getIdFirstUser())->getFirstName() . ":  " . $pvmsg->getContent();
            } else {
                $lastMsg = "me:  " . $pvmsg->getContent();
            }

            // $msgTime =

            $testCol[] = array("firstName" => $us->getFirstName()
            , "id" => $us->getId()
            , "lastName" => $us->getLastName()
            , "imgUrl" => $us->getImgUrl()
            , "lastMsgSent" => $lastMsg);

        }


        return $this->render('private_message/index.html.twig', [
            'private_messages' => $privateMessages,
            'private_conversations' => $conversations,
            'b3athtelhom' => $eliB3atelhom,
            'ba3thouli' => $eliBa3thouli,
            'merg' => $merg,
            'uscnv' => $ussrs,
            'testc' => $testCol,
            'msgs' => $messages,
        ]);
    }


    /**
     * @Route("/conversation/json/{id}", name="conversation_index", methods={"GET"})
     */
    public function getAllConversationJson(int $id , EntityManagerInterface $entityManager): Response
    {

        $privateMessages = $entityManager
            ->getRepository(PrivateMessage::class)
            ->findAll();

        $eliB3atelhom = array();
        $eliBa3thouli = array();

        $conversations = $entityManager
            ->getRepository(PrivateMessage::class)->showAllUserWithJaw($id);

        foreach ($conversations as $c) {
            if ($id == $c->getIdFirstUser()) {
                if (!in_array($c->getIdSecondUser(), $eliB3atelhom)) {
                    $eliB3atelhom[] = $c->getIdSecondUser();
                }
            } elseif ($id == $c->getIdSecondUser()) {
                if (!in_array($c->getIdFirstUser(), $eliBa3thouli)) {
                    $eliBa3thouli[] = $c->getIdFirstUser();
                }
            }
        }

        $merg = array_unique(array_merge($eliB3atelhom, $eliBa3thouli), SORT_REGULAR);

        $ussrs = $entityManager
            ->getRepository(User::class)
            ->findBy(array('id' => $merg));

       return $this->json($ussrs,200,[],['groups'=>'user:read']);
    }

    /**
     *
     * @Route ("/load/msgs/{id}" , name="load_user_messages")
     * @param PrivateMessage $privateMessage
     * @param EntityManagerInterface $entityManager
     * @param PrivateMessageRepository $repository
     * @return Response
     */
    public function loadConversation(int $id, PrivateMessage $privateMessage, EntityManagerInterface $entityManager, PrivateMessageRepository $repository):
    Response
    {
        $messages = $entityManager
            ->getRepository(PrivateMessage::class)->findAllMessagesBetweenTwoUsers2($this->getUser()->getId(), $entityManager->getRepository(User::class)->find($id));

        return $this->json($messages, 200);

    }

    /**
     *
     * @Route ("/load/msgs/json/{id}/{idF}" , name="load_user_messages_json")
     * @param PrivateMessage $privateMessage
     * @param EntityManagerInterface $entityManager
     * @param PrivateMessageRepository $repository
     * @return Response
     */
    public function loadJsonConversation(int $id,int $idF,NormalizerInterface $normalizer
        , PrivateMessage $privateMessage, EntityManagerInterface $entityManager, PrivateMessageRepository $repository):
    Response
    {
        $messages = $entityManager
            ->getRepository(PrivateMessage::class)->findAllMessagesBetweenTwoUsers2($idF, $entityManager->getRepository(User::class)->find($id));

        $messagesNormalized = $normalizer->normalize($messages);



        $presentation = json_encode($messagesNormalized, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $response = new Response($presentation);
        $response->headers->set('Content-Type', 'application/json');
        return $response;
        // $this->json($messages, 200);

    }

    /**
     * @Route ("/msgsent/{id}" , name="messages_sent")
     */
    public function create(int $id, FlashyNotifier $flashy,Request $request)
    {
        $body = $request->getContent();
        $data = json_decode($body, true);

        if ($id == $this->getUser()->getId()) {
            $flashy->success("New message: ".$data['msg'], "http://127.0.0.1:8000/private/message/");
        }
        return $this->json("", 200);


//        return new Response('success');
//        return $this->redirectToRoute('home');
    }


    /**
     * @Route("/send/msg/{id}", name="private_message_send", methods={"GET", "POST"})
     */
    public function new(int $id, Request $request, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $body = $request->getContent();
        $data = json_decode($body, true);
        $privateMessage = new PrivateMessage();

        $privateMessage->setCreatedAt(new \DateTime());

        $privateMessage->setIdFirstUser($data['idFirstUser']);

        $privateMessage->setIdSecondUser($data['idSecondUser']);

        $privateMessage->setContent($data['content']);

        $entityManager->persist($privateMessage);
        $entityManager->flush();

//        if($id == $this->getUser()->getId()){
        $flashy->success('Event created!', 'http://your-awesome-link.com');
//        }

        return new Response($body);
        //return $this->redirectToRoute('private_message_index', [], Response::HTTP_SEE_OTHER);

    }


    /**
     * @Route("/send/msg/json", name="private_message_send", methods={"GET", "POST"})
     */
    public function sendJson( Request $request, EntityManagerInterface $entityManager, FlashyNotifier $flashy): Response
    {
        $body = $request->getContent();

        $data = json_decode($body, true);

        $privateMessage = new PrivateMessage();

        $privateMessage->setCreatedAt(new \DateTime());

        $privateMessage->setIdFirstUser($data['idFirstUser']);

        $privateMessage->setIdSecondUser($data['idSecondUser']);

        $privateMessage->setContent($data['content']);

        $entityManager->persist($privateMessage);
        $entityManager->flush();


        return $this->json($data,201,[],['groups'=>'message:read']);


    }

    /**
     * @Route("/new", name="private_message_new", methods={"GET", "POST"})
     */
    public function send(Request $request, EntityManagerInterface $entityManager): Response
    {
        $privateMessage = new PrivateMessage();
        $form = $this->createForm(PrivateMessageType::class, $privateMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $privateMessage->setCreatedAt(new \DateTime());
            $entityManager->persist($privateMessage);
            $entityManager->flush();

            return $this->redirectToRoute('private_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('private_message/new.html.twig', [
            'private_message' => $privateMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="private_message_show", methods={"GET"})
     */
    public function show(PrivateMessage $privateMessage): Response
    {
        return $this->render('private_message/show.html.twig', [
            'private_message' => $privateMessage,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="private_message_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, PrivateMessage $privateMessage, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(PrivateMessageType::class, $privateMessage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('private_message_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('private_message/edit.html.twig', [
            'private_message' => $privateMessage,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="private_message_delete", methods={"POST"})
     */
    public function delete(Request $request, PrivateMessage $privateMessage, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $privateMessage->getId(), $request->request->get('_token'))) {
            $entityManager->remove($privateMessage);
            $entityManager->flush();
        }

        return $this->redirectToRoute('private_message_index', [], Response::HTTP_SEE_OTHER);
    }
}
