<?php

namespace AppBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class BlogController extends Controller
{
    /** @var integer */
    const ENTRY_LIMIT = 5;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $authorRepository;

    /** @var \Doctrine\Common\Persistence\ObjectRepository */
    private $blogPostRepository;

    /**
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->blogPostRepository = $entityManager->getRepository('AppBundle:BlogPost');
        $this->authorRepository = $entityManager->getRepository('AppBundle:Author');
    }

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

        return $this->render('blog/entries.html.twig', [
            'blogPosts' => $this->blogPostRepository->getAllPosts($page, self::ENTRY_LIMIT),
            'totalBlogPosts' => $this->blogPostRepository->getPostCount(),
            'authors' => $this->authorRepository->findAll(),
            'entryLimit' => self::ENTRY_LIMIT,
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
        $blogPost = $this->blogPostRepository->findOneBySlug($slug);

        if (!$blogPost) {
            $this->addFlash('error','Unable to find entry!');

            return $this->redirectToRoute('entries');
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
        $author = $this->authorRepository->findOneByUsername($name);

        if (!$author) {
            $this->addFlash('error','Unable to remove author!');

            return $this->redirectToRoute('entries');
        }

        return $this->render('blog/author.html.twig', [
            'author' => $author
        ]);
    }
}
