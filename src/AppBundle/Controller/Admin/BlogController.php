<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Author;
use AppBundle\Entity\BlogPost;
use AppBundle\Form\AuthorFormType;
use AppBundle\Form\EntryFormType;
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
        $page = 1;

        if ($request->get('page')) {
            $page = $request->get('page');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $author = $entityManager->getRepository('AppBundle:Author')
            ->findOneByUsername($this->getUser()->getUserName());

        $blogPosts = [];

        if ($author) {
            $blogPosts = $entityManager->getRepository('AppBundle:BlogPost')
                ->findByAuthor($author);
        }

        return $this->render('admin/blog/entries.html.twig', [
            'blogPosts' => $blogPosts,
            'page' => $page
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
        $entityManager = $this->getDoctrine()->getManager();
        $author = $entityManager->getRepository('AppBundle:Author')
            ->findOneByUsername($this->getUser()->getUserName());

        $blogPost = $entityManager->getRepository('AppBundle:BlogPost')->findOneBySlug($entryId);

        if (!$blogPost) {
            // No blog post,
            exit;
        }

        if ($author !== $blogPost->getAuthor()) {
            // Not same author
            exit;
        }

        $entityManager->remove($blogPost);
        $entityManager->flush();

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

        $entityManager = $this->getDoctrine()->getManager();
        $author = $entityManager->getRepository('AppBundle:Author')
            ->findOneByUsername($this->getUser()->getUserName());
        $blogPost->setAuthor($author);

        $form = $this->createForm(EntryFormType::class, $blogPost);
        $form->handleRequest($request);

        // Check is valid
        if ($form->isValid()) {
            $entityManager->persist($blogPost);
            $entityManager->flush($blogPost);

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
        $entityManager = $this->getDoctrine()->getManager();

        // Check whether user already has an author.
        if ($entityManager->getRepository('AppBundle:Author')->findOneByUsername($this->getUser()->getUserName())) {
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
            $entityManager->persist($author);
            $entityManager->flush($author);

            $request->getSession()->set('user_is_author', true);
            $this->addFlash('success','Congratulations! You are now an author.');

            return $this->redirectToRoute('admin_entries');
        }

        return $this->render('admin/blog/author.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
