<?php

namespace App\Controller;

use App\Entity\Book;
use App\Entity\Category;
use App\Form\CategoryType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/category')]
class CategoryController extends AbstractController
{
    // Отображение всех категорий
    #[Route('/category_form', name: 'app_category_form')]
    public function categoryList(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('Category/category_form.html.twig', [
            'categories' => $categories,
        ]);
    }

    // Создание новой категории
    #[Route('/category_create', name: 'app_category_create')]
    public function createCategory(Request $request, EntityManagerInterface $entityManager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        $category->setCreatedAt();
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($category);
            $entityManager->flush();

            return $this->redirectToRoute('app_category_form');
        }

        return $this->render('Category/_create.html.twig', [
            'form_category' => $form->createView(),
        ]);
    }

    // Редактирование категории
    #[Route('/category_edit/{id}', name: 'app_category_edit_single')]
    public function editCategory(Category $category, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('app_category_form');
        }

        return $this->render('Category/_edit.html.twig', [
            'form_category' => $form->createView(),
            'category' => $category,
        ]);
    }

    // Удаление категории
    #[Route('/category_delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute('app_category_form');
    }

    #[Route('/check-category-books/{id}', name: 'check_category_books', methods: ['GET'])]
    public function checkCategoryBooks(Category $category, EntityManagerInterface $em): JsonResponse
    {
        // Проверяем, есть ли связанные книги
        $bookCount = $em->getRepository(Book::class)->count(['category' => $category->getId()]);

        return new JsonResponse([
            'hasBooks' => $bookCount > 0
        ]);
    }
}