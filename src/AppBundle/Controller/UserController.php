<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use AppBundle\Form\Type\UserType;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class UserController extends Controller
{
    public function indexAction(Request $request)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);

        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $userRepository->createQueryBuilder('u')
            ->orderBy('u.surname', 'ASC')
            ->addOrderBy('u.name', 'ASC');

        $users = $this->get('knp_paginator')
            ->paginate(
                $queryBuilder->getQuery(),
                $request->query->getInt('page', 1),
                20
            );

        return $this->render(
            'AppBundle:user:index.html.twig',
            [
                'users' => $users,
                'title' => 'Users list',
            ]
        );
    }

    public function editAction(Request $request, $idUser)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);

        if ($idUser) {
            $user = $userRepository->find($idUser);

            if (!$user) {
                $this->addFlash('error', sprintf('User with id: %s not found', $idUser));

                return $this->redirect(
                    $this->generateUrl('app_user_index')
                );
            }
            $title = 'Edit user';
        } else {
            $user = new User();
            $title = 'Add user';
        }

        $form = $this->createForm(new UserType(), $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($idUser) {
                $user->setUpdatedAt(new \DateTime());
            } else {
                $user->setCreatedAt(new \DateTime());
                $user->setIsActive(true);
            }

            $userRepository->save($user);

            $this->addFlash('success', sprintf('User %s %s has been updated', $user->getName(), $user->getSurname()));

            return $this->redirect(
                $this->generateUrl('app_user_index')
            );
        }

        return $this->render(
            'AppBundle:user:edit.html.twig',
            [
                'form' => $form->createView(),
                'title' => $title,
            ]
        );
    }

    public function deleteAction($idUser)
    {
        /** @var UserRepository $userRepository */
        $userRepository = $this->getDoctrine()
            ->getRepository(User::class);

        /** @var User $user */
        $user = $userRepository->find($idUser);
        if (!$user) {
            $this->addFlash('error', sprintf('User with id: %s not found', $idUser));

            return $this->redirect(
                $this->generateUrl('app_user_index')
            );
        }

        $fullName = sprintf('% %s', $user->getName(), $user->getSurname());

        $userRepository->remove($user);

        $this->addFlash('success', sprintf('User %s has been removed', $fullName));

        return $this->redirect(
            $this->generateUrl('app_user_index')
        );
    }
}