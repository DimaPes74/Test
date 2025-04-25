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
    #[Route('/category_form', name: 'app_category_form')]
    public function categoryList(EntityManagerInterface $entityManager): Response
    {
        $categories = $entityManager->getRepository(Category::class)->findAll();

        return $this->render('Category/category_form.html.twig', [
            'categories' => $categories,
        ]);
    }

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

    #[Route('/category_delete/{id}', name: 'app_category_delete')]
    public function deleteCategory(Category $category, EntityManagerInterface $entityManager): Response
    {
        try {
            $entityManager->remove($category);
            $entityManager->flush();
            return $this->redirectToRoute('app_category_form');
        } catch (\Doctrine\DBAL\Exception\ForeignKeyConstraintViolationException $e) {
            $this->addFlash('error', 'This category cannot be deleted because it is associated with one or more books.');
            return $this->redirectToRoute('app_category_form');
        }
    }
}