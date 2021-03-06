<?php

namespace BlogBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

//use AppBundle\Controller\ArticleController;

class DefaultController extends Controller
{

    /**
     * Lists all article entities.
     * @Route("/blog/index.html", name="blog_index_route")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em
            ->getRepository('AppBundle:Article')
            ->findAll();

        return $this->render('@Blog/blog_view/index.html.twig', [
            'articles' => $articles]);
    }

    /**
     * @Route("/blog/show/{slug}", name="blog_show_route")
     * @Method("GET")
     */
    public function singleAction($slug)
    {
        $em = $this->getDoctrine()->getManager();

        $article = $em
            ->getRepository('AppBundle:Article')
            ->findOneBy(['slug' => $slug]);

        if (!$article) {
            throw $this->createNotFoundException("No article found for $slug");
        }

        return $this->render('@Blog/blog_view/articleShow.html.twig', ['article' => $article]);
    }

    /**
     * @Route("/blog/category/{category}", name="blog_category_route")
     * @Method("GET")
     */
    public function categoryAction($category = null)
    {
        $em = $this->getDoctrine()->getManager();

        $categoriesList = $em
            ->getRepository('AppBundle:Category')
            ->findAll();

        if($category) {

            $categorybytitle = $em
                ->getRepository('AppBundle:Category')
                ->findBy(['title' => $category]);

            if (!$categorybytitle) {
                throw $this->createNotFoundException("No category found for $category");
            }

            return $this->render('@Blog/blog_view/categoryShow.html.twig', ['categorybytitle' => $categorybytitle['0']]);
        }

        return $this->render('@Blog/blog_view/categoriesList.html.twig', ['categories' => $categoriesList]);
    }

    /**
     * @Route("/blog/search/", name="blog_search_route")
     * @Method({"GET", "POST"})
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function searchArticlesAction(Request $request)
    {
        $form = $this->createForm('AppBundle\Form\SearchType');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $articlesRepository = $this->getDoctrine()->getRepository('AppBundle:Article');
            $inquiry = $form->getData()['inquiry'];
            $result = $articlesRepository->searchBy($inquiry);

            return $this->render('@Blog/blog_view/articleSearch.html.twig', [
                'articles' => $result,
                'inquiry' => $inquiry,
                'form' => $form->createView(),
            ]);
        }

        return $this->render('@Blog/blog_view/search_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }


}
