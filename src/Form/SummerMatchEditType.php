<?php

namespace App\Form;

use App\Entity\SummerMatch;
use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use App\Repository\TeamRepository;
use App\Repository\TeamsHaveMatchesRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class SummerMatchEditType extends SummerMatchType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);
        $data = $builder->getData();
        if ($data instanceof SummerMatch) {
            $matchID = $data->getId();
        }

        $builder->add('winner', EntityType::class, [
            'class' => Team::class,
            'query_builder' => function (TeamRepository $er) use ($matchID) {
                return $er->createQueryBuilder('t')
                    ->join(TeamsHaveMatches::class, 'thm', 'WITH', 't.id = thm.teamsHaveMatches')
                    ->join(SummerMatch::class, 'sm', 'WITH', 'sm.id = thm.matchesHaveTeams')
                    ->where('thm.matchesHaveTeams = :matchID')
                    ->setParameter('matchID', $matchID);
            },
            'choice_label' => 'name',
        ]);

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SummerMatch::class,
        ]);
    }
}