<?php

namespace Katana\LogBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;


class FormType  extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('country', 'entity', array(
                'multiple' => true,
                'empty_value' => '',
                'class' => 'KatanaDictionaryBundle:Country',
                'label' => false,
                'required' => false,
                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('c')->add('orderBy', 'c.code ASC');},
                'attr' => array('class' => "chzn", 'ng-model'=>'formData.country' , 'ng-change'=>'submit()'),
                'constraints' => array(
                    new Assert\Type(array('type' => 'object')),
                )
            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'katana_logbundle_formtype';
    }
}