<?php

namespace App\Form;

use App\Entity\Video;
use App\Entity\Channel;

use App\Entity\Program;
use App\Entity\Enum\StatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class VideoFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('video', Select2EntityType::class, [
                'multiple' => false,
                'remote_route' => 'video_select',
                'class' => Video::class,
                'primary_key' => 'id',
                'text_property' => 'title',
                'minimum_input_length' => 0,
                'page_limit' => 10,
                'delay' => 250,
                'cache' => true,
                'cache_timeout' => 60000, // if 'cache' is true
                'allow_clear' => true,
                'placeholder' => 'Vidéo',
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
                'allow_clear' => true,
                'placeholder' => 'Programme',
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
                'allow_clear' => true,
                'placeholder' => 'Chaîne',
            ])
            ->add('status' , ChoiceType::class, [
                'choices' => array_flip(StatusEnum::STATUS),
                'attr' => [
                    'class' => 'choice',
                ],
                'placeholder' => 'Etat',
                'required' => false,
            ])
        ;
    }
}
