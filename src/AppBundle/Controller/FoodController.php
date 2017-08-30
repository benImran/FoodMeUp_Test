<?php
namespace AppBundle\Controller;

use AppBundle\Entity\Food;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
/**
 * Food controller.
 *
 * @Route("")
 */
class FoodController extends Controller
{
    /**
     * Lists all food entities.
     *
     * @Route("/", name="food_index")
     * @Method({"GET", "POST"})
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $foods = $em->getRepository('AppBundle:Food');
        if(isset($_POST['search'])){
            $query = $foods->createQueryBuilder('p')
                ->where('p.name LIKE :name')
                ->setParameter('name', '%'.$_POST['search'].'%')
                ->getQuery();
            $foods = $query->getResult();
        } else {
            $foods = $em->getRepository('AppBundle:Food')->findAll();
        }
        return $this->render('food/index.html.twig',array(
            'foods' => $foods
        ));
    }
    /**
     *
     * @Route("/search", name="food_search")
     * @Method("POST")
     */
    public function searchAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $foods = $em->getRepository('AppBundle:Food');
        $query = $foods->createQueryBuilder('p')
            ->where('p.name LIKE :name')
            ->setParameter('name', '%'.$_POST['search'].'%')
            ->getQuery();
        $foods = $query->getResult();
        return $this->render('food/index.html.twig',array(
            'foods' => $foods
        ));
    }
    /**
     * Creates a new food entity.
     *
     * @Route("/new", name="food_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $food = new Food();
        $form = $this->createForm('AppBundle\Form\FoodType', $food);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($food);
            $em->flush();
            return $this->redirectToRoute('food_index');
        }
        return $this->render('food/new.html.twig', array(
            'food' => $food,
            'form' => $form->createView(),
        ));
    }
    /**
     * Finds and displays a food entity.
     *
     * @Route("/{id}", name="food_show")
     * @Method("GET")
     */
    public function showAction(Food $food)
    {
        $deleteForm = $this->createDeleteForm($food);
        return $this->render('food/show.html.twig', array(
            'food' => $food,
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Displays a form to edit an existing food entity.
     *
     * @Route("/{id}/edit", name="food_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Food $food)
    {
        $deleteForm = $this->createDeleteForm($food);
        $editForm = $this->createForm('AppBundle\Form\FoodType', $food);
        $editForm->handleRequest($request);
        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            return $this->redirectToRoute('food_edit', array('id' => $food->getId()));
        }
        return $this->render('food/edit.html.twig', array(
            'food' => $food,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }
    /**
     * Deletes a food entity.
     *
     * @Route("/{id}", name="food_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Food $food)
    {
        $form = $this->createDeleteForm($food);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($food);
            $em->flush();
        }
        return $this->redirectToRoute('food_index');
    }
    /**
     * Creates a form to delete a food entity.
     *
     * @param Food $food The food entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Food $food)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('food_delete', array('id' => $food->getId())))
            ->setMethod('DELETE')
            ->getForm()
            ;
    }
}