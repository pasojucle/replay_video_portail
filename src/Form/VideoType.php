<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Channel;
use App\Entity\Program;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class VideoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('broadcastAt', DateType::class, [
                'label' => 'Date de diffusion',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'attr' => [
                    'class' => 'js-datepicker',
                    'autocomplete' => "off",
                ],
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('program', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'program_select',
                'class' => Program::class,
                'primary_key' => 'id',
                'text_property' => 'title',
                'minimum_input_length' => 0,
                'page_limit' => 10,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000, // if 'cache' is true
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('url', UrlType::class, [
                'label' => 'url',
                'row_attr' => ['class' => 'form-group'],
            ])
            ->add('channel', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'channel_select',
                'class' => Channel::class,
                'primary_key' => 'id',
                'text_property' => 'title',
                'minimum_input_length' => 0,
                'page_limit' => 10,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000, // if 'cache' is true
                'row_attr' => ['class' => 'form-group'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Video::class,
        ]);
    }
}
