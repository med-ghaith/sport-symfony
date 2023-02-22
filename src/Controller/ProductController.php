<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Form\CartType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Serializer\Serializer;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * @Route("/product")
 */
class ProductController extends AbstractController
{

    /**
     * @Route("/listproduct", name="listproduct", methods={"GET"})
     */
    public function index(ProductRepository $productRepository, PaginatorInterface $paginator, Request $request): Response
    {
        $donnees = $productRepository->findAll();

        $products = $paginator->paginate(
            $donnees,
            $request -> query->getInt('page',1),4
        );
        return $this->render('product/product-listing.html.twig', [
            'products' => $products ,
        ]);
    }
    /**
     * @Route("/listproduct_row", name="listproduct_row", methods={"GET"})
     */
    public function index1(ProductRepository $productRepository): Response
    {
        return $this->render('product/indexrow.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }
    /**
     * @Route("/listproductAdmin", name="product_index", methods={"GET"})
     */
    public function indexAdmin(ProductRepository $productRepository): Response
    {
        return $this->render('product/indexAdmin.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/newproduct", name="product_new", methods={"GET", "POST"})
     */
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $file = $form->get('imageUrl')->getData();

            $newFilename = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $product->setImageUrl($newFilename);
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_show", methods={"GET", "POST"})
     */
    public function show(Product $product,Request $request, EntityManagerInterface $entityManager): Response
    {
        $user=$this->getDoctrine()->getRepository(User::class)->find(33);
        $product=$this->getDoctrine()->getRepository(Product::class)->find($product->getId());
        $cart = new Cart();
        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $cart->setUser($user);
            $cart->setProduct($product);
            $cart->setPrice($product->getPrice() * $cart->getQuantity());


            $entityManager->persist($cart);
            $entityManager->flush();

            return $this->redirectToRoute('cart_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/show.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }
    /**
     * @Route("/{id}/show", name="product_showAdmin", methods={"GET"})
     */
    public function showAdmin(Product $product): Response
    {
        return $this->render('product/showAdmin.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form->get('imageUrl')->getData();
            $newFilename = md5(uniqid()).'.'.$file->guessExtension();
            try {
                $file->move(
                    $this->getParameter('upload_directory'),
                    $newFilename
                );
            } catch (FileException $e) {
            }

            $product->setImageUrl($newFilename);
            $entityManager->flush();

            return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="product_delete", methods={"POST"})
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('product_index', [], Response::HTTP_SEE_OTHER);
    }

    //********************* Add PRODUCT Mobile ****************************
    /**
     * @Route ("/Product/addProduct")
     * @Method ("POST")
     */
    public function addProduct(Request $request){
        $product = new Product();
        $name = $request->query->get("name");


        $Description= $request->query->get("description");
        $price = $request->query->get("price");
        $em = $this->getDoctrine()->getManager();



        $product->setName($name);
        $product->setDescription($Description);
        $product->setPrice($price);

        $em->persist($product);
        $em->flush();
        $serializer = new Serializer([new ObjectNormalizer()]);
        $formatted = $serializer->normalize($product);
        return new JsonResponse($formatted);


    }
    //********************* affiche PROD Mobile ****************************
    /**
     * @Route("/Product/GetProduct", name="GetProduct")
     */
    public function GetProduct(ProductRepository $repository, SerializerInterface $serializer)
    {
        $product = $repository->findAll();
        $data1 = array();

        foreach ($product as $key => $prod) {
            $data1[$key]['id'] = $prod->getId();
            $data1[$key]['name'] = $prod->getName();
            $data1[$key]['description'] = $prod->getDescription();
            $data1[$key]['price'] = $prod->getPrice();



        }
        return new JsonResponse($data1);
    }


    /**
     * @Route("/prod/apiSortPrice", name="product_sortprice_api")
     */
    public function sortjsonPrice(SerializerInterface $Normalizer) : Response
    {
        $evenements=$this->getDoctrine()->getRepository(Product::class)->SortByPrice();
        $jsonContent=$Normalizer->serialize($evenements,'json',['groups'=>'prod']);
        $response=new Response($jsonContent);
        $response->headers->set('Content-Type','application/json');
        return $response;
    }

}