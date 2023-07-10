<?php

namespace App\Form;

use App\Entity\SummerMatch;
use App\Entity\Team;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
class SummerMatchEditType extends SummerMatchType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->add('winner', EntityType::class, [
            'class' => Team::class,
            'choice_label' => 'name',]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SummerMatch::class,
        ]);
    }
}