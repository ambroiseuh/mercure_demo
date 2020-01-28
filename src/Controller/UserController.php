<?php


namespace App\Controller;


use App\Entity\User;
use App\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;


class UserController extends AbstractController
{

    private $em;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->em = $entityManager;
        $this->session = $session;
    }

    /**
     *
     * @Route("/", name="homepage")
     * @param Request $request
     * @param PublisherInterface $publisher
     * @return RedirectResponse|Response
     */
    public function indexAction(Request $request, PublisherInterface $publisher) {

        if($this->session->has('user') && $this->session->get('user')) {
            return $this->redirectToRoute('message');
        }

        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $auteur = $form->getData();
            $user = new User();
            $user->setName($auteur['name']);
            $this->em->persist($user);
            $this->em->flush();
            $this->session->set('user', $user);

            $update = new Update(
                'http://super-presente.com/user',
                json_encode([
                        'id' => $user->getId(),
                        'name' => $user->getName(),
                    ]
                )
            );

            $publisher($update);

            return $this->redirectToRoute('message');
        }

        return $this->render('message/homepage.html.twig', [
            'form' => $form->createView(),
        ]);

    }

}