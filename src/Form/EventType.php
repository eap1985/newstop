<?php

namespace eap1985\NewsTopBundle\Form;

use eap1985\NewsTopBundle\Entity\NewsTop;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Название'
            ])
            ->add('time', DateTimeType::class, [
                'label' => 'Начало',
                'widget' => 'single_text',
            ])
            ->add('arhiv', DateTimeType::class, [
                'label' => 'Конец',
                'widget' => 'single_text',
            ])
            ->add('opisanie', TextareaType::class, ['label' => 'Описание','attr' => ['class' => 'tinymce']])
            ->add('avtor', TextType::class, ['label' => 'Автор'])
            ->add('metka', TextType::class, ['label' => 'Раздел'])
            ->add('archived', CheckboxType::class, [
                'label' => 'В архиве',
                'required' => false
            ])->addEventListener(
                FormEvents::SUBMIT,
                [$this, 'onSubmit']
            )
        ;
    }

    public function onSubmit(FormEvent $event)
    {

        $data = $event->getData();

        if(!$data->isArchived()) {


            $data->setArchived(0);
            $event->setData($data);


        }


    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NewsTop::class,
        ]);
    }
}
