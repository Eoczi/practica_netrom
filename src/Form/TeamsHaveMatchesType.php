<?php

namespace App\Form;

use App\Entity\Team;
use App\Entity\TeamsHaveMatches;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamsHaveMatchesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            //->add('nrPoints')
            ->add('teamsHaveMatches', EntityType::class, [
                'label' => 'Alege echipa',
                'class' => Team::class,
                'choice_label' => 'name',])
            ->add('goals', null, [
                'label' => 'Numarul de goluri a echipei'
            ])
            //->add('matchesHaveTeams')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TeamsHaveMatches::class,
        ]);
    }
}
