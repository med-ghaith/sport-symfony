<?php

namespace App\Controller;



use App\Entity\Muscle;
use App\Utils\ExcelToJson;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
//require 'ExcelToJson.php';
/**
 * @Route("/bot")
 */
class ChatBotController extends AbstractController
{
    /**
     * @Route("/", name="chat_bot")
     */
    public function index(EntityManagerInterface $entityManager): Response
    {
        $excelCnv = new ExcelToJson();
        $salutation_json = $excelCnv->convertColumnArrayToJsonForJSBot("SALUTATION");
        $lebes_json = $excelCnv->convertColumnArrayToJsonForJSBot("LEBES");
        $wenti_json = $excelCnv->convertColumnArrayToJsonForJSBot("WENTI");
        $wazn_json = $excelCnv->convertColumnArrayToJsonForJSBot("WAZN");
        $toul_json = $excelCnv->convertColumnArrayToJsonForJSBot("TOUL");
        $equipment_json = $excelCnv->convertColumnArrayToJsonForJSBot("EQUIPMENT");
        $positive_json = $excelCnv->convertColumnArrayToJsonForJSBot("POSITIVE-ANSWER");
        $negative_json = $excelCnv->convertColumnArrayToJsonForJSBot("NEGATIVE-ANSWER");
        $equip_base_json = $excelCnv->convertColumnArrayToJsonForJSBot("EQUIPMENT-BASE");
        $bad_words_json = $excelCnv->convertColumnArrayToJsonForJSBot("BAD-WORDS");
        $muscles = $entityManager
            ->getRepository(Muscle::class)->findAll();
        $muscles_arr = array();
        foreach ($muscles as $musc) {
            $muscles_arr[] = array(
                'name' => $musc->getName(),
            );
        }
       // $muscles = json_encode($muscles);

        return $this->render('chat-bot/index.html.twig', [
            'salutation_json' => $salutation_json,
            'lebes_json' => $lebes_json,
            'wenti_json' => $wenti_json,
            'wazn_json' => $wazn_json,
            'toul_json' => $toul_json,
            'equipment_json' => $equipment_json,
            'positive_json' => $positive_json,
            'negative_json' => $negative_json,
            'equip_base_json' => $equip_base_json,
            'bad_words_json' => $bad_words_json,
            'muscles' => $muscles_arr,
            'controller_name' => 'ChatController',
        ]);
    }

    /**
     * @Route("/baseequip", name="baseEquip_bot", methods={"GET"})
     */
    public function base(): Response
    {
        $response = new Response();
        $excelCnv = new ExcelToJson();
        $baseEquip_json = $excelCnv->convertColumnArrayToJson("EQUIPMENT-BASE");

        $response->setContent($baseEquip_json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/negat", name="negat_bot", methods={"GET"})
     */
    public function negat(): Response
    {
        $response = new Response();
        $excelCnv = new ExcelToJson();
        $negat_json = $excelCnv->convertColumnArrayToJson("NEGATIVE-ANSWER");

        $response->setContent($negat_json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/posit", name="posit_bot", methods={"GET"})
     */
    public function positive(): Response
    {
        $response = new Response();
        $excelCnv = new ExcelToJson();
        $positive_json = $excelCnv->convertColumnArrayToJson("POSITIVE-ANSWER");

        $response->setContent($positive_json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/salutation", name="salutation_bot")
     */
    public function salutation(EntityManagerInterface $entityManager): Response
    {
        $excelCnv = new ExcelToJson();
        $salutation_json = $excelCnv->convertColumnArrayToJson("SALUTATION");

        return new Response($salutation_json);
    }

    /**
     * @Route("/wazn", name="weight_bot", methods={"GET"})
     */
    public function wazn(): Response
    {
        $response = new Response();
        $excelCnv = new ExcelToJson();
        $wazn_json = $excelCnv->convertColumnArrayToJson("WAZN");

        $response->setContent($wazn_json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/toul", name="toul_bot", methods={"GET"})
     */
    public function toul(): Response
    {
        $response = new Response();
        $excelCnv = new ExcelToJson();
        $toul_json = $excelCnv->convertColumnArrayToJson("TOUL");

        $response->setContent($toul_json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','application/json; charset=utf-8');

        return $response;
    }

    /**
     * @Route("/equip", name="equip_bot", methods={"GET"})
     */
    public function equip(): Response
    {
        $response = new Response();

        $excelCnv = new ExcelToJson();
        $equip_json = $excelCnv->convertColumnArrayToJson("EQUIPMENT");

        $response->setContent($equip_json);
        $response->setStatusCode(200);
        $response->headers->set('Content-Type','application/json; charset=utf-8');

        return $response;
    }




}
