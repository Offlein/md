<?php
namespace MD\DebateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DebateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'attr' => array(
                'ng-model' => 'edit.name',
                'placeholder' => 'New Debate Name',
            ),
        ));
        $builder->add('description', 'textarea', array(
            'attr' => array(
                'ng-model' => 'edit.description',
                'placeholder' => 'New Description',
            ),
        ));
    }

    public function getName()
    {
        return 'debate';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MD\DebateBundle\Entity\Debate',
            'csrf_protection' => false,
        ));
    }

}
