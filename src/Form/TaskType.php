<?php

namespace App\Form;

use App\Entity\Task;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TaskType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description')
            ->add('priority')
            ->add('startDate', DateType::class, [
                'widget' => 'single_text'
            ])
            
            ->add('endDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('realStartDate', DateType::class, [
                'widget' => 'single_text'
            ])
            ->add('realEndDate', DateType::class, [
                'widget' => 'single_text'
            ])
            // ->add('createdAt')
            // ->add('updatedAt')
            ->add('user')
            ->add('project')
            ->add('status')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Task::class,
        ]);
    }
}
