<?php


namespace App\Controller;


use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;


class MessageController extends AbstractController
{

    private $em;
    private $session;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session)
    {
        $this->em = $entityManager;
        $this->session = $session;
    }

    /**
     * @Route("/message", name="message")
     * @param Request $request
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function newMessage(Request $request) {

        $listMessages = $this->em->getRepository(Message::class)->findAll();
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $message Message
             */
            $message = $form->getData();

            if(!$auteur = $this->session->get('auteur')) {
                $auteur = $this->generateName();
            }


            $message->setDate(new \DateTime());
            $message->setAuteur($auteur);
            $this->em->persist($message);
            $this->em->flush();

            return $this->redirectToRoute('message');
        }

        return $this->render('message/messages.html.twig', [
            'form' => $form->createView(),
            'messages' => $listMessages
        ]);
    }

    public function generateName() {

        $names = array(
            'Dark',
            'Pika',
            'Volde',
            'Aragorn',
            'Voldo',
            'Luc',
            'Obi',
            'Olaf',
        );

        $surnames = array(
            'Vador',
            'Wan',
            'Skywalker',
            'De la foret',
            'Chu',
            'Pinochio',
            'Mort',
            'Simpson',
            'Youpi',
            'Zlatan'
        );

        $random_name = $names[mt_rand(0, sizeof($names) - 1)];
        $random_surname = $surnames[mt_rand(0, sizeof($surnames) - 1)];
        $name = $random_name . ' ' . $random_surname;
        $this->session->set('auteur', $name);
        return $random_name . ' ' . $name;
    }


}