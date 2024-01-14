<?php

namespace App\Form;

use App\Entity\SummerMatch;
use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use App\Repository\TeamRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class SummerMatchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {

        $data = $builder->getData();
        if ($data instanceof SummerMatch) {
            $matchID = $data->getId();
        }
        $builder->add('startDate', DateType::class, [
                'label' => 'Data in care este sustinut meciul: '
            ])->add('winner', EntityType::class, [
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
