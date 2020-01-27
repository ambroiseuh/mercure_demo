<?php


namespace App\Controller;


use App\Entity\Message;
use App\Entity\User;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Mercure\Publisher;
use Symfony\Component\Mercure\PublisherInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;


class MessageController extends AbstractController
{

    private $em;
    private $session;
    private $mercureUrl;

    public function __construct(EntityManagerInterface $entityManager, SessionInterface $session, $mercureUrl)
    {
        $this->em = $entityManager;
        $this->session = $session;
        $this->mercureUrl = $mercureUrl;
    }

    /**
     * @Route("/message", name="message")
     * @param Request $request
     * @param PublisherInterface $publisher
     * @param SerializerInterface $serializer
     * @return RedirectResponse|Response
     * @throws \Exception
     */
    public function newMessage(Request $request, PublisherInterface $publisher, SerializerInterface $serializer) {

        if(!$this->session->has('user') && !$this->session->get('user') && !$this->session->get('user') instanceof User) {
            return $this->redirectToRoute('homepage');
        }

        /**
         * @var User $currentUser
         */
        $currentUser = $this->session->get('user');
        $currentUser = $this->em->getRepository(User::class)->find($currentUser->getId());
        $listMessages = $this->em->getRepository(Message::class)->findAll();
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /**
             * @var $message Message
             */
            $message = $form->getData();
            $message->setDate(new \DateTime());
            $message->setAuteur($currentUser);
            $this->em->persist($message);
            $this->em->flush();

            $update = new Update(
                'http://super-presente.com/message',
                $serializer->serialize($message,'json')
            );

            $publisher($update);

            return $this->redirectToRoute('message');
        }
        return $this->render('message/messages.html.twig', [
            'form' => $form->createView(),
            'messages' => $listMessages,
            'user' => $currentUser,
            'mercureUrl' => $this->mercureUrl
        ]);
    }

}