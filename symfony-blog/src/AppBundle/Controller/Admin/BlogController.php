<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Author;
use AppBundle\Entity\BlogPost;
use AppBundle\Form\AuthorFormType;
use AppBundle\Form\EntryFormType;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @package AppBundle\Controller\Admin
 *
 * @Route("/admin")
 */
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
     * @Route("/", name="admin_index")
     * @Route("/entries", name="admin_entries")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function entriesAction(Request $request)
    {
        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());

        $blogPosts = [];

        if ($author) {
            $blogPosts = $this->blogPostRepository->findByAuthor($author);
        }

        return $this->render('admin/blog/entries.html.twig', [
            'blogPosts' => $blogPosts
        ]);
    }

    /**
     * @Route("/delete-entry/{entryId}", name="admin_delete_entry")
     *
     * @param $entryId
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteEntryAction($entryId)
    {
        $blogPost = $this->blogPostRepository->findOneById($entryId);
        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());

        if (!$blogPost || $author !== $blogPost->getAuthor()) {
            $this->addFlash('error','Unable to remove entry!');

            return $this->redirectToRoute('admin_entries');
        }

        $this->entityManager->remove($blogPost);
        $this->entityManager->flush();

        $this->addFlash('success','Entry was deleted!');

        return $this->redirectToRoute('admin_entries');
    }

    /**
     * @Route("/create-entry", name="admin_create_entry")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createEntryAction(Request $request)
    {
        $blogPost = new BlogPost();

        $author = $this->authorRepository->findOneByUsername($this->getUser()->getUserName());
        $blogPost->setAuthor($author);

        $form = $this->createForm(EntryFormType::class, $blogPost);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isValid()) {
            $this->entityManager->persist($blogPost);
            $this->entityManager->flush($blogPost);

            $this->addFlash('success','Congratulations! Your post is created. It may take 30 seconds for Contentful\'s to process the entry though.');

            return $this->redirectToRoute('admin_entries');
        }

        return $this->render('admin/blog/entry_form.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/author/create", name="admin_author_create")
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAuthorAction(Request $request)
    {
        // Check whether user already has an author.
        if ($this->authorRepository->findOneByUsername($this->getUser()->getUserName())) {
            // Redirect to dashboard.
            $this->addFlash('error','Unable to create author, author already exists!');

            return $this->redirectToRoute('admin_entries');
        }

        $author = new Author();
        $author->setUsername($this->getUser()->getUserName());

        // Use Author form.
        $form = $this->createForm(AuthorFormType::class, $author);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isValid()) {
            $this->entityManager->persist($author);
            $this->entityManager->flush($author);

            $request->getSession()->set('user_is_author', true);
            $this->addFlash('success','Congratulations! You are now an author.');

            return $this->redirectToRoute('admin_entries');
        }

        return $this->render('admin/blog/author.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
