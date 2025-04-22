<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\CategoryType;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/add-form', name: 'app_category_add_form')]
    public function addForm(): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('app_category_create')
        ]);

        return $this->render('Category/_form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/category/create', name: 'app_category_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $category->setCreatedAt();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Категория добавлена');
            return $this->redirectToRoute('app_book_new');
        }

        $this->addFlash('error', 'Ошибка при добавлении категории');
        return $this->redirectToRoute('app_book_new');
    }

    #[Route('/category/{id}/edit-form', name: 'app_category_edit_form')]
    public function editForm(Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category, [
            'action' => $this->generateUrl('app_category_update', ['id' => $category->getId()])
        ]);

        return $this->render('Category/_form.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
            'modal' => true
        ]);
    }

    #[Route('/category/{id}/update', name: 'app_category_update', methods: ['POST'])]
    public function update(Request $request, Category $category, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Категория обновлена');
            return $this->redirectToRoute('app_book_new');
        }

        $this->addFlash('error', 'Ошибка при обновлении категории');
        return $this->redirectToRoute('app_book_new');
    }

    #[Route('/category/{id}/delete', name: 'app_category_delete')]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $bookCount = $em->getRepository(Book::class)->count(['category' => $category]);

        if ($bookCount > 0) {
            $this->addFlash('error', 'Нельзя удалить категорию, так как с ней связаны книги');
        } else {
            $em->remove($category);
            $em->flush();
            $this->addFlash('success', 'Категория удалена');
        }

        return $this->redirectToRoute('app_book_new');
    }
}
