<?php

namespace App\Form;

use ReflectionClass;
use ReflectionMethod;
use App\Entity\Enum\StatusEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class LogFilterType extends AbstractType
{
    private TranslatorInterface $translator;
    public function __construct(TranslatorInterface $translator)
    {
       $this->translator = $translator; 
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('createdAt', DateType::class, [
                'placeholder' => 'Date',
                'widget' => 'single_text',
                'html5' => false,
                'format' => 'dd/MM/yyyy',
                'row_attr' => ['class' => 'form-group'],
                'required' => false,
                'attr' => [
                    'class' => 'js-datepicker',
                    'autocomplete' => "off",
                ],
            ])
            ->add('route' , ChoiceType::class, [
                'choices' => array_flip($this->getRoutes()),
                'placeholder' => 'Type d\'action',
                'required' => false,
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

    private function getRoutes()
    {
        $rc = new ReflectionClass('App\Controller\WebserviceController');
        $routes = [];
        foreach ($rc->getMethods() as $method) {
            $rm = new ReflectionMethod('App\Controller\WebserviceController', $method->getName());
            if (preg_match('#@Route\(".*", name="(\w+)".*\)#', $rm->getDocComment(), $matches)) {
                $routes[$matches[1]] = $this->translator->trans($matches[1]);
            }
        }

        return $routes;
    }
}
