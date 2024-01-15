<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use App\Entity\User;
use App\Repository\TeamRepository;
use App\Repository\TeamsHaveMatchesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamsHaveMatchesType extends AbstractType
{
    public function __construct (private Security $security, private EntityManagerInterface $entityManager) {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $this->security->getUser();
        $userEntityId = $this->entityManager->getRepository(User::class)->findOneBy(['email' => $user->getUserIdentifier()])->getId();

        $builder
            ->add('teamsHaveMatches', EntityType::class, [
                'label' => 'Choose team',
                'class' => Team::class,
                'choice_label' => 'name',
                'query_builder' => function (TeamRepository $er) use ($userEntityId) {
                    return $er->createQueryBuilder('t')
                        ->where('t.user = :userEntityId')
                        ->setParameter('userEntityId', $userEntityId);
                },
            ])
            ->add('goals', null, [
                'label' => 'Number of goals of the team'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeamsHaveMatches::class,
        ]);
    }
}
