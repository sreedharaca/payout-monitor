<?php

namespace Katana\OfferBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\EntityRepository;


class OfferFilterType  extends AbstractType{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('affiliate', 'entity', array(
                'multiple' => false,
                'empty_value' => '',
                'class' => 'KatanaAffiliateBundle:Affiliate',
                'label' => 'Партнер',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('a')->add('orderBy', 'a.id ASC');},
                'attr' => array('class' => "chzn", 'ng-model'=>'formData.affiliate' , 'ng-change'=>'do()'),
                'constraints' => array(
                    new Assert\Type(array('type' => 'object')),
                )
            ))
            ->add('country', 'entity', array(
                'multiple' => true,
                'empty_value' => '',
                'class' => 'KatanaDictionaryBundle:Country',
                'label' => 'Страна',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('c')->add('orderBy', 'c.code ASC');},
                'attr' => array('class' => "chzn"),
                'constraints' => array(
                    new Assert\Type(array('type' => 'object')),
                )
            ))
            ->add('platform', 'entity', array(
                'multiple' => true,
                'empty_value' => '',
                'class' => 'KatanaDictionaryBundle:Platform',
                'label' => 'Платформа',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('p')->add('orderBy', 'p.id ASC');},
                'attr' => array('class' => "chzn"),
                'constraints' => array(
                    new Assert\Type(array('type' => 'object')),
                )
            ))
            ->add('device', 'entity', array(
                'multiple' => true,
                'empty_value' => '',
                'class' => 'KatanaDictionaryBundle:Device',
                'label' => 'Девайс',
                'required' => false,
                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('d')->add('orderBy', 'd.name ASC');},
                'attr' => array('class' => "chzn"),
                'constraints' => array(
                    new Assert\Type(array('type' => 'object')),
                )
            ))
            ->add('incentive', 'checkbox', array(
                    'value' => false,
                    'attr' => array('title'=>'Оплачено', 'ng-model'=>'formData.incentive' , 'ng-change'=>'do()'),
                    'label' => 'Incentive',
                    //'attr'     => array('checked'   => 'checked'),
                    'required'  => false)
            )
            ->add('new', 'checkbox', array(
                    'value' => false,
                    'attr' => array('title'=>'Оплачено', 'ng-model'=>'formData.new' , 'ng-change'=>'do()'),
                    'label' => 'Новые',
                    //'attr'     => array('checked'   => 'checked'),
                    'required'  => false)
            )
            ->add('search', 'text', array(
//                    'value' => false,
                    'attr' => array('placeholder'=>'Введите название', 'ng-model'=>'formData.search' /*, 'ng-change'=>'do()'*/),
                    'label' => 'Поиск',
                    //'attr'     => array('checked'   => 'checked'),
                    'required'  => false)
            )

//            ->add('building', 'entity', array(
//                'multiple' => false,
//                'empty_value' => '',
//                'class' => 'MaxiBookingHotelBundle:Building',
//                'label' => 'Корпус',
//                'required' => false,
//                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('h')->where('h.active = 1')->andWhere('h.deleted = 0')->add('orderBy', 'h.name ASC');},
//                'attr' => array('class' => "chzn-select"),
//                'constraints' => array(
//                    new Assert\Type(array('type' => 'object')),
//                )
//            ))
//            ->add('roomType', 'entity', array(
//                'multiple' => false,
//                'empty_value' => '',
//                'class' => 'MaxiBookingHotelBundle:RoomType',
//                'label' => 'Тип номера',
//                'required' => false,
//                'query_builder' => function(EntityRepository $er) {return $er->createQueryBuilder('h')->where('h.active = 1')->andWhere('h.deleted = 0')->add('orderBy', 'h.name ASC');},
//                'attr' => array('class' => "chzn-select"),
//                'constraints' => array(
//                    new Assert\Type(array('type' => 'object')),
//                )
//            ))
//            ->add('dateRange', 'text', array(
//                'label' => 'Даты',
//                'mapped'=>false,
//            ))
//            ->add('begin', 'date',array(
//                'label' => 'Дата заезда',
//                'widget' => 'single_text',
//                'format' => 'dd.MM.yyyy',
//                'required'  => true,
//                'attr' => array('class' => 'date-picker span6', 'data-date-format' => 'dd.mm.yyyy'),
//                'constraints' => array(
//                    new Assert\NotBlank(),
//                    new Assert\Date(),
//                )
//            ))
//            ->add('end', 'date',array(
//                'label' => 'Дата отъезда',
//                'widget' => 'single_text',
//                'format' => 'dd.MM.yyyy',
//                'required'  => true,
//                'attr' => array('class' => 'date-picker span6', 'data-date-format' => 'dd.mm.yyyy'),
//                'constraints' => array(
//                    new Assert\NotBlank(),
//                    new Assert\Date(),
//                )
//            ))
//            ->add('mode', 'choice', array(
//                'label' => 'Режим',
//                'choices'   => array('simple' => 'Простой', 'freeRooms'=>'Свободные номера', 'pivot' => 'Сводный'),
//                'required'  => true,
//                'attr' => array('class' => "chzn-select"),
//                'constraints' => array(
//                    new Assert\NotBlank(),
//                )
//
//            ))
        ;
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'katana_offerbundle_offerfiltertype';
    }
}