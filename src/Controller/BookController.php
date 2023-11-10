<?php

namespace App\Controller;

use App\Entity\Book;
use App\Form\BookType;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }

    #[Route('/addBook', name: 'app_add_book')]
    public function addBook(Request $request):Response
    {
        $book= new Book();

        $form=$this->createForm(BookType::class,$book);
        $form->add('Save',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) // Check if the form was submitted and is valid
        {
            $author = $book->getAuthor();
            if ($author) {
                $author->incrementNbBooks();
            }
            $em=$this->getDoctrine()->getManager();// Get the Doctrine entity manager

            $em->persist($book);// to remember the Author entity and be prepared to save it to the database
            $em->flush();// commit (save) the changes to the database

            return $this->redirectToRoute("app_afficheBook"); //Redirect to the "app_affiche" route if the form submission was successful
        }
        return $this->render('/book/books.html.twig',
            ["form"=>$form->createView(),
            ]);
    }

    #[Route('/affichebook', name: 'app_afficheBook')]
    public function affichage(BookRepository $repository):Response
    {
        $allBooks=$repository->findAll();
        $book=$repository->findBy(['published'=>true]); // affichage avec condition sur published where = True
        $bookUnpublished=$repository->findBy(['published'=>false]);
        return $this->render("book/afficheBook.html.twig",["book"=>$book,
            "unB"=>$bookUnpublished,
            "allBook"=>$allBooks]);
    }

    #[Route('/editBook/{id}', name: 'app_editBook')]
    public function UpdateBook(BookRepository $repository,$id,Request $request):Response
    {
        $book = $repository->find($id);
        $form=$this->createForm(BookType::class,$book);
        $form->add('EditBook',SubmitType::class);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em=$this->getDoctrine()->getManager();
            $em->flush();
            return $this->redirectToRoute("app_afficheBook");
        }
        return $this->render("book/editBook.html.twig",["form"=>$form->createView()]);
    }

#[Route('/deleteBook/{id}', name: 'app_delBook')]
public function delete($id,BookRepository $repository):Response
{
    $book = $repository->find($id);
    if(!$book){
        throw $this->createNotFoundException('book non trouvé');
    }
    $em=$this->getDoctrine()->getManager();
    $em->remove($book);
    $em->flush();

    return $this->redirectToRoute('app_afficheBook');
}

    #[Route('/affichebookById/{id}', name: 'app_afficheBookDetails')]
    public function affichageById(BookRepository $repository,$id):Response
    {
        $bookById=$repository->find($id); //get book by Id
        return $this->render("book/afficheBookById.html.twig",[
            "bookById"=>$bookById]);
    }



}
