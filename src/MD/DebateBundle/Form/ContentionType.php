<?php
namespace MD\DebateBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ContentionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', 'text', array(
            'attr' => array(
                'ng-model' => 'edit.name',
                'placeholder' => 'New Contention',
            ),
            'label' => 'Your contention:'
        ));
        $builder->add('aff', 'choice', array(
            'expanded' => true,
            'choices' => array(
                true => 'Affirmative',
                false => 'Negative'
            ),
            'required' => true,
            'label' => 'Is this contention for the affirmative or the negative?'
        ));
    }

    public function getName()
    {
        return 'contention';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MD\DebateBundle\Entity\Contention',
        ));
    }
}
