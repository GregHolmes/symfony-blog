<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    /**
     * @Route("/", name="index")
     * @Route("/entries", name="entries")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entriesAction(Request $request)
    {
        $page = 1;

        if ($request->get('page')) {
            $page = $request->get('page');
        }

        $blogPostRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:BlogPost');
        $blogPosts = $blogPostRepo->findAll();

        return $this->render('blog/entries.html.twig', [
            'blogPosts' => $blogPosts,
            'authors' => $this->getAuthors(),
            'page' => $page
        ]);
    }

    /**
     * @param $slug
     *
     * @Route("/entry/{slug}", name="entry")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entryAction($slug)
    {
        $blogPostRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:BlogPost');

        $blogPost = $blogPostRepo->findOneBySlug($slug);

        if (!$blogPost) {
            // Return error.
            // Redirect to index
        }

        return $this->render('blog/entry.html.twig', [
            'blogPost' => $blogPost
        ]);
    }

    /**
     * @param string $name
     *
     * @Route("/author/{name}", name="author")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function authorAction($name)
    {
        $authorRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Author');
        $author = $authorRepo->findOneByUsername($name);

        if (!$author) {
            // Add flash and redirect to index
            exit;
        }

        return $this->render('blog/author.html.twig', [
            'author' => $author
        ]);
    }

    /**
     * @return array
     */
    private function getAuthors()
    {
        $authorRepo = $this->getDoctrine()->getManager()->getRepository('AppBundle:Author');

        return $authorRepo->findAll();
    }
}
