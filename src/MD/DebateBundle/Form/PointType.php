<?php
namespace MD\DebateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PointType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('body', 'textarea', array('label' => 'Body:'));
        $builder->add('source', 'text', array('label' => 'Source:'));
    }

    public function getName()
    {
        return 'point';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MD\DebateBundle\Entity\Point',
        ));
    }
}
