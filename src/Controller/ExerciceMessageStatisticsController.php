<?php

namespace App\Controller;

use App\Entity\Exercice;
use App\Form\ExerciceType;
use App\Repository\EquipmentRepository;
use App\Repository\ExerciceRepository;
use App\Repository\MuscleRepository;
use App\Repository\PrivateMessageRepository;
use App\Repository\UserRepository;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\CalendarChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\ComboChart;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\Histogram;
use CMEN\GoogleChartsBundle\GoogleCharts\Charts\PieChart;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/msg/statistics")
 */
class ExerciceMessageStatisticsController extends AbstractController
{
    /**
     * @Route("/", name="exercice_index", methods={"GET"})
     */
    public function index(ExerciceRepository $exerciceRepository, EquipmentRepository  $equipmentRepository
        , MuscleRepository $muscleRepository, UserRepository $userRepository
        ,PrivateMessageRepository $privateMessageRepository): Response
    {

        $equipNumber = $equipmentRepository->countEquipment();
        $exercNumber = $exerciceRepository->countExercice();
        $musclesNumber = $muscleRepository->countMuscles();
        $usersNumber = $userRepository->countUsers();
//        $equipNumber = 12;
        $pieChart = new PieChart();
        $pieChart->getData()->setArrayToDataTable(
            [['Task', 'Hours per Day'],
                ['equip Number',   $equipNumber],
                ['Exercice Number', $exercNumber],
                ['Muscle Number',  $musclesNumber],
                ['Active Users', $usersNumber]
            ]
        );
        $pieChart->getOptions()->setTitle('How many gym manager ');
        $pieChart->getOptions()->setHeight(500);
        $pieChart->getOptions()->setWidth(900);
        $pieChart->getOptions()->getTitleTextStyle()->setBold(true);
        $pieChart->getOptions()->getTitleTextStyle()->setColor('#009900');
        $pieChart->getOptions()->getTitleTextStyle()->setItalic(true);
        $pieChart->getOptions()->getTitleTextStyle()->setFontName('Arial');
        $pieChart->getOptions()->getTitleTextStyle()->setFontSize(20);



        $combo = new ComboChart();
        $month1 = $privateMessageRepository->countMessages(01);
        $month2 = $privateMessageRepository->countMessages(02);
        $month3 = $privateMessageRepository->countMessages(03);
        $month4 = $privateMessageRepository->countMessages(04);
        $combo->getData()->setArrayToDataTable([
            ['Month', 'Messages', ],
            ['2022/01', $month1 , ],
            ['2022/02',  $month2,    ],
            ['2022/03',  $month3,    ],
            ['2022/04',  $month4,    ]
        ]);
        $combo->getOptions()->setTitle('Monthly Coffee Production by Country');
        $combo->getOptions()->getVAxis()->setTitle('Cups');
        $combo->getOptions()->getHAxis()->setTitle('Month');
        $combo->getOptions()->setSeriesType('bars');

        $series5 = new \CMEN\GoogleChartsBundle\GoogleCharts\Options\ComboChart\Series();
        $series5->setType('line');
        $combo->getOptions()->setSeries([5 => $series5]);

        $combo->getOptions()->setWidth(900);
        $combo->getOptions()->setHeight(500);
        return $this->render('message_exercice_statistic/index.html.twig', array('piechart' => $pieChart, 'combo' => $combo));

    }

}
