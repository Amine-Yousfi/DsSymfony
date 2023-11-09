<?php

namespace App\Controller;

use App\Entity\Author;
use App\Form\AuthorType;
use App\Repository\AuthorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }

    #[Route('/showAuthor/{name}', name: 'app_showAuthor')]
    public function showAuthor($name):Response
    {
        return $this->render('author/show.html.twig',[
            'name'=>$name,
        ]);
    }

    #[Route('/showList', name: 'app_showlist')]
    public function list():Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>
                'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
                ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
                'taha.hussein@gmail.com', 'nb_books' => 300),
        );
        return $this->render('author/list.html.twig',[
            'authors'=>$authors,
        ]);
    }

    #[Route('/details/{id}', name: 'app_details')]
    public function authorDetails($id):Response
    {
        $authors = array(
            array('id' => 1, 'picture' => '/images/Victor-Hugo.jpg','username' => 'Victor Hugo', 'email' =>
                'victor.hugo@gmail.com ', 'nb_books' => 100),
            array('id' => 2, 'picture' => '/images/william-shakespeare.jpg','username' => ' William Shakespeare', 'email' =>
                ' william.shakespeare@gmail.com', 'nb_books' => 200 ),
            array('id' => 3, 'picture' => '/images/Taha_Hussein.jpg','username' => 'Taha Hussein', 'email' =>
                'taha.hussein@gmail.com', 'nb_books' => 300),
        );
        return $this->render('author/showAuthor.html.twig',[
            "authors"=>$authors,
            "id"=>$id,
        ]);
    }

    #[Route('/add', name: 'app_addNew')]
        public function Add(Request $request):Response
        {
            $author=new Author();

            $form=$this->CreateForm(AuthorType::class,$author);// Create a form using the AuthorType form class and associate it with the Author object
            $form->add('Ajouter',SubmitType::class);// Add a "Ajouter" button of type SubmitType to the form
            $form->handleRequest($request);// Handle the incoming HTTP request data with the form

            if($form->isSubmitted() && $form->isValid()) // Check if the form was submitted and is valid
            {
                $em=$this->getDoctrine()->getManager();// Get the Doctrine entity manager

                $em->persist($author);// to remember the Author entity and be prepared to save it to the database
                $em->flush();// commit (save) the changes to the database

                return $this->redirectToRoute("app_affiche"); //Redirect to the "app_affiche" route if the form submission was successful
            }
            return $this->render('/author/form.html.twig',
                ["form"=>$form->createView(),
                ]);
        }

    #[Route('/affiche', name: 'app_affiche')]
    public function affichage(AuthorRepository $repository):Response
    {
        $author=$repository->findAll();
        return $this->render("author/affiche.html.twig",["author"=>$author]);
    }


    #[Route('/edit/{id}', name: 'app_edit')]
    public function editAuthor(AuthorRepository $repository,$id,Request $request):Response
    {
        $author = $repository->find($id); // find User by Id
        $form=$this->createForm(AuthorType::class,$author);
        $form->add('Edit',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("app_affiche");
        }
        return $this->render("author/edit.html.twig",["form"=>$form->createView()]);
    }

    #[Route('/delete/{id}', name: 'app_delete')]
    public function delete($id,AuthorRepository $repository):Response
    {
        $author = $repository->find($id);
        if(!$author){
            throw $this->createNotFoundException('author non trouvé');
        }
        $em=$this->getDoctrine()->getManager();
        $em->remove($author);
        $em->flush();

        return $this->redirectToRoute('app_affiche');
    }
}

