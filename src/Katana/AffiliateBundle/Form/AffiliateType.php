<?php

namespace Katana\AffiliateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class AffiliateType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('apiUrl')
            ->add('offerUrl')
            ->add('partialLoading', 'checkbox', array(
                'required' => false
            ))
            ->add('active', 'checkbox', array(
                'required' => false
            ))
            ->add('needAuth', 'checkbox', array(
                'required' => false
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Katana\AffiliateBundle\Entity\Affiliate'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'katana_affiliatebundle_affiliate';
    }
}
