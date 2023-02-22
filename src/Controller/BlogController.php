<?php

namespace App\Controller;

use App\Entity\Blog;
use App\Entity\User;
use App\Entity\BlogReview;
use App\Entity\BlogCategory;
use App\Entity\BlogLike;
use App\Form\BlogType;
use App\Form\BlogReviewType;
use App\Repository\BlogRepository;
use App\Repository\BlogReviewRepository;
use App\Repository\BlogLikeRepository;
use App\Repository\BlogCategoryRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Knp\Component\Pager\PaginatorInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerInterface;

use Symfony\Component\Serializer\Serializer;

  use \Expalmer\PhpBadWords\PhpBadWords as BadWords;

/**
 * @Route("/blog")
 */
class BlogController extends AbstractController
{

    /**
     * @Route("/pdf/{id}", name="pdf-down")
     */
    public function pdfgenrator(BlogRepository $blogRepository, BlogCategoryRepository $blogCategoryRepository, $id): Response
    {
        // Configure Dompdf according to your needs
        $pdfOptions = new Options();
        $pdfOptions->set('defaultFont', 'Arial');
        
        // Instantiate Dompdf with our options
        $dompdf = new Dompdf($pdfOptions);
        $blog=$blogRepository->find($id);
        $cat = $blogCategoryRepository->findAll();
        
        // Retrieve the HTML generated in our twig file
        $html = $this->renderView('blog/pdf.html.twig', [
            'blog' => $blog,
            'categories' => $cat,
        ]);
        
        // Load HTML to Dompdf
        $dompdf->loadHtml($html);
        
        // (Optional) Setup the paper size and orientation 'portrait' or 'portrait'
        $dompdf->setPaper('A3', 'portrait');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser (force download)
        $dompdf->stream("Blog.pdf", [
            "Attachment" => false
        ]);  
        
    }

     /**
     * @Route("/admin", name="blog-admin", methods={"GET"})
     */
    public function indexAdmin(BlogRepository $blogRepository, BlogCategoryRepository $blogCategoryRepository): Response
    {
        $cat = $blogCategoryRepository->findAll();
        return $this->render('blog/admin.html.twig', [
            'blogs' => $blogRepository->findAll(),
            'categories' => $cat,
        ]);
    }

    /**
     * @Route("/admin/{id}", name="blog-validate", methods={"GET"})
     */
    public function validate(BlogRepository $blogRepository, BlogCategoryRepository $blogCategoryRepository, $id, EntityManagerInterface $entityManager): Response
    {
        $cat = $blogCategoryRepository->findAll();
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
       

        $blog->setVerified(true);
        $entityManager->persist($blog);
        $entityManager->flush();

        return $this->redirectToRoute('blog-admin');
    }

    /**
     * @Route("/", name="blog_index", methods={"GET"})
     */
    public function index(BlogRepository $blogRepository, BlogCategoryRepository $blogCategoryRepository, Request $request,PaginatorInterface $paginator): Response
    {

        $cat = $blogCategoryRepository->findAll();
        $blogs = $this->getDoctrine()->getRepository(Blog::class)->findAll();
        $blogs = $paginator->paginate(
            $blogs, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
        2 /*limit per page*/
        );
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'categories' => $cat,
        ]);
    }

    /**
     * @Route("/blogCat/{id}", name="blog_cat", methods={"GET"})
     */
    public function blogbycategory(BlogRepository $blogRepository, $id, BlogCategoryRepository $blogCategoryRepository,  Request $request,PaginatorInterface $paginator): Response
    {
        $cat = $blogCategoryRepository->findAll();
       $blogs = $blogRepository->listBlogByCategory($id);
       $blogs = $paginator->paginate(
        $blogs, /* query NOT result */
        $request->query->getInt('page', 1), /*page number*/
    2 /*limit per page*/
    );
        return $this->render('blog/index.html.twig', [
            'blogs' => $blogs,
            'categories' => $cat,
        ]);
    }

    /**
     * @Route("/new", name="blog_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $blog = new Blog();
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);
        $user = $this->getDoctrine()->getRepository(User::class)->find(33);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('image')->getData();


            $filename = md5(uniqid()) . '.' . $file->guessExtension();
            $file->move($this->getParameter('upload_directory'), $filename);
            $blog->setImage($filename);
            $blog->setUser($user);

            $entityManager->persist($blog);
            $entityManager->flush();
            

            return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/new.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/det/{id}", name="blog-show", methods={"GET"})
     */
    public function show($id): Response
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        return $this->render('blog/show.html.twig', [
            'blog' => $blog,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="blog_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, $id, EntityManagerInterface $entityManager): Response
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $form = $this->createForm(BlogType::class, $blog);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('blog/edit.html.twig', [
            'blog' => $blog,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="blog-delete", methods={"POST"})
     */
    public function delete(Request $request, Blog $blog, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$blog->getId(), $request->request->get('_token'))) {
            $entityManager->remove($blog);
            $entityManager->flush();
        }

        return $this->redirectToRoute('blog_index', [], Response::HTTP_SEE_OTHER);
    }

    

    /**
     * @Route("/like/{id}/like", name="blog-like")
     */
    public function like(Blog $blog, EntityManagerInterface $entityManager, BlogLikeRepository $blogLikeRepository): Response
    {
       // $user=$this->getUser();
        $user = $this->getDoctrine()->getRepository(User::class)->find(38);

        if (!$user)  return $this->json(['code' => 403, 'message' => 'Unauthorized'], 403);

        if($blog->isLikedByUser($user)){
            $like=$blogLikeRepository->findOneBy([
                'blog'=>$blog,
                'user'=>$user
            ]);
            $entityManager->remove($like);
            $entityManager->flush();
            return $this->json(['code' => 200, 'message' => 'like deleted','likes' => $blogLikeRepository->count(['blog'=>$blog])],200);
        }
        $like=new BlogLike();
        $like->setBlog($blog)
            ->setUser($user);
        $entityManager->persist($like);
        $entityManager->flush();
        return $this->json(['code' => 200, 'message' => 'like added','likes'=> $blogLikeRepository->count(['blog'=>$blog])],200);

    }


    /**
     * @Route("/detail/detailBlog{id}", name="detail-Blog")
     */
    public function readblog(int $id,Request $request,Blog $blog,EntityManagerInterface $entityManager,BlogReviewRepository $blogReviewRepository,BlogRepository $rep, BlogCategoryRepository $blogCategoryRepository): Response
    {
        $blog = $rep->find($id);
        $value = $blog->getView();
        $value = $value + 1 ;
        $blog->setView($value);
        $entityManager->flush();
   

        $idblog = $request->get('id');

        
        $cat = $blogCategoryRepository->findAll();
        $myDictionary = array(
            array("ass","among","hi"),
            "anal",
            "butt"
          );
          $badwords = new BadWords();
  
       
        //// initialisation note
        $blogReview = new BlogReview();
        $noteform = $this->createForm(BlogReviewType::class,$blogReview);
        $noteform->add('add',SubmitType::class);
        $noteform->handleRequest($request);
        $user = $this->getDoctrine()->getRepository(User::class)->find(38);

        if ($noteform->isSubmitted() && $noteform->isValid()) {

            $blogReview->setIdblog($blog);
            $blogReview->setUser($user);
            $badwords->setDictionaryFromArray( $myDictionary )
                     ->setText( $blogReview->getComment() );
                     if( $badwords->check() ){
                        
                     } else {
            $em=$this->getDoctrine()->getManager();

            $em->persist($blogReview);
            $em->flush();




            return $this->redirectToRoute('detail-Blog', ['id' => $blog->getId()]);
        }
        }
        $e = $this->getDoctrine()->getRepository(Blog::class);
        $blog = $e->find($id);
        //dd($blog);
        return $this->render('blog/blogDetail.html.twig',
          [
              'blog'=> $blog,
              'noteform' => $noteform->createView(),
              'rates'=> $blogReviewRepository->findByName($blog->getTitle()),
              'blogrates'=> $blogReviewRepository->findAll(),
              'categories' => $cat,
          ]);

    }

     /**
     * @Route("/delete/{id}", name="deleteB")
     */
    public function deleteBlog($id)
    {
        $blog = $this->getDoctrine()->getRepository(Blog::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($blog);
        $em->flush();
        return $this->redirectToRoute("blog-admin");
    }

    

    /**
     * @Route("/stats", name="blog-stats")
     */
    public function statistiques(BlogRepository $blogRepository)
    { $blog = $blogRepository->findAll();
        /* you can also inject "FooRepository $repository" using autowire */
         
       /* $count = $repository->count();
        dd($count); */
       
           /*  $countbydate= $repository->createQueryBuilder('a')
            ->select('SUBSTRING(datefin,1,10) As datedufin, COUNT(a) as count')
            ->groupby('datedufin')
            ->getQuery()
            ->getResult(); */
       //
       $repository = $this->getDoctrine()->getRepository(Blog::class);
       $count= $repository->createQueryBuilder('u')
            ->select('(u.view)')
            ->groupby('u.view')
            ->getQuery()
            ->getResult();
            
            $countdate= $repository->createQueryBuilder('a')
            ->select('(a.title)')
            ->groupby('a.title')
            ->getQuery()
            ->getResult();
        foreach($blog as $blog){
            
            $date[] = $blog->getView();
            
        }
        
 
            for ($i = 0; $i < count($count); ++$i){
                
                $count1[] = $count[$i][1] ;  
                $countdate1[] = $countdate[0][1];
            }
            
        
        return $this->render('blog/stats.html.twig', [ 
            'date' => json_encode($date ),
            'count1' => json_encode($count1),
            'countdate1' => json_encode($countdate1),
            
          
            
        ]);   
    }
    


/**
 * @Route("/api/liste", name="liste", methods={"GET"})
 */
public function liste(BlogRepository $blogRepo, NormalizerInterface $normalizer, SerializerInterface $serializerInterface)
{
    // On récupère la liste des articles
    $blogs = $blogRepo->findAll();

    

   $json= $serializerInterface->serialize($blogs, 'json', ['groups' => 'blog']);
  
   $response = new Response($json, 200, [
       "Content_Type" => "application/json"
   ]);
   return $response;
}

/**
 * @Route("/api/det/{id}", name="lisblog-det", methods={"GET"})
 */
public function blogdet(BlogRepository $blogRepo, NormalizerInterface $normalizer, SerializerInterface $serializerInterface, $id)
{
    // On récupère la liste des articles
    $blogs = $blogRepo->find($id);

    

   $json= $serializerInterface->serialize($blogs, 'json', ['groups' => 'blog']);
  
   $response = new Response($json, 200, [
       "Content_Type" => "application/json"
   ]);
   return $response;
}

/**
     * @param $id
     * @Route("/deleteblApi/{id}", name="deleteblApi")
     * methods={"GET"}
     */
    public function deleteBlogApi(Request $request, $id)
    {

        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository(Blog::class)->find($id);
        if ($blog) {
            $em->remove($blog);
            $em->flush();
            return new JsonResponse('Deleted', 200);
        }
        return new JsonResponse('Error not found', 500);
    }

     /**
     * @Route("/new/bl", name="ad-bl")
     * * methods={"POST"}
     */
    public function addjson(Request $req, SerializerInterface $serializerInterface, EntityManagerInterface $em)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = new Blog();
        $blog->setTitle($req->get('title'));
        $blog->setDescription($req->get('description'));
        
        $file = $req->get('image');
        
     
     $blog->setImage($req->get('image'));
        $blog->setView(0);
        //$blog->setCategory($req->get('category'));
        $user = $this->getDoctrine()->getRepository(User::class)->find(38);
        $cat = $this->getDoctrine()->getRepository(BlogCategory::class)->find(1);
        $blog->setUser($user);
        $blog->setBlogCat($cat);
        

        $em->persist($blog);
        $em->flush();
        $json = $serializerInterface->serialize($blog, 'json', ['groups' => 'blog']);
        return new Response('blog aded succefully');
    }

    /**
     * @Route("/editbl/{id}", name="edbl")
     * * methods={"POST"}
     */
    public function editjson(Request $req, SerializerInterface $serializerInterface, EntityManagerInterface $em, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $blog = $em->getRepository(Blog::class)->find($id);
        $blog->setTitle($req->get('title'));
        $blog->setDescription($req->get('description'));
        $blog->setImage($req->get('image'));
        $blog->setCategory($req->get('category'));
        $cat = $this->getDoctrine()->getRepository(BlogCategory::class)->find(1);
        $blog->setBlogCat($cat);
        

        $em->persist($blog);
        $em->flush();
        $json = $serializerInterface->serialize($blog, 'json', ['groups' => 'blog']);
        return new Response('blog updated succefully');
    }
   
}
