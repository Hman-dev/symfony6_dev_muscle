<?php

namespace App\Controller;

use App\Entity\Ingredient;
use App\Form\IngredientType;
use App\Repository\IngredientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class IngredientController extends AbstractController
{
    /**
     * This controller display all ingredients
     *
     * @param IngredientRepository $repository
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    #[Route('/ingredient', name: 'app_ingredient', methods: ['GET'])]
    public function index(IngredientRepository $repository, PaginatorInterface $paginator, Request $request): Response
    {
        //Dans un idex() on a fait une injection de dépendances en rajoutant IngredientsRipository pour avoir acces
        //aux méthodes qu'y sont à l'intérieur. 
        $ingredients = $repository->findAll();

        $ingredients = $paginator->paginate(
            $repository->findAll(),
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('pages/ingredient/index.html.twig', [
            'ingredients' => $ingredients
        ]);

        //Deuxième méthode, même choses que la première

        // dd($ingredients);

        // return $this->render('pages/ingredient/index.html.twig', [
        //     'ingredients' => $repository->findAll()
        // ]);
    }

    /**
     * This controller show a form which create an ingredient
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    #[Route('/ingredient/nouveau', 'ingredient.new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $manager
    ): Response {
        $ingredient = new Ingredient();
        $form = $this->createForm(IngredientType::class, $ingredient);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();


            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'votre ingredient a été créé avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    // #[Route('/ingredient/edition/{id}','ingredient.edit', methods:['GET','POST'])]
    // public function edit(IngredientRepository $repository,int $id) : Response
    // {
    //     //Ici on va recupérer l'ingredient venant de l'Id

    //     // $ingredient = $repository->findOneBy(["id" => $id]);
    //     // $form = $this->createForm(IngredientType::class, $ingredient);

    //     // return $this->render('pages/ingredient/edit.html.twig', [
    //     //     'form' => $form->createView()
    //     // ]);


    // }

    #[Route('/ingredient/edition/{id}', 'ingredient.edit', methods: ['GET', 'POST'])]
    public function edit(Ingredient $ingredient, Request $request, EntityManagerInterface $manager): Response
    {
        //Methode avec le @paramconverter      

        $form = $this->createForm(IngredientType::class, $ingredient);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ingredient = $form->getData();


            $manager->persist($ingredient);
            $manager->flush();
            $this->addFlash(
                'success',
                'votre ingredient a été modifié avec succès !'
            );

            return $this->redirectToRoute('app_ingredient');
        }

        return $this->render('pages/ingredient/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    #[Route('/ingredient/suppression/{id}', 'ingredient.delete', methods: ['GET'])]
    public function delete(
        EntityManagerInterface $manager, 
        Ingredient $ingredient
        ): Response
    {
        // if (!$ingredient) {
           
        //     $this->addFlash(
        //         'success',
        //         'L\'ingredient en question n\'a pas été trouvé'
        //     );

        //     return $this->redirectToRoute('app_ingredient');
        // }

        $manager->remove($ingredient);
        $manager->flush();

        $this->addFlash(
            'success',
            'votre ingredient a été supprimé avec succès !'
        );
        return $this->redirectToRoute('app_ingredient');
    }
}
