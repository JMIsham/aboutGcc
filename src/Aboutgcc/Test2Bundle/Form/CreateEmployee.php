<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 2/10/2017
 * Time: 5:27 PM
 */

namespace Aboutgcc\Test2Bundle\Form;



use Symfony\Component\Form\AbstractType;
//use Symfony\Component\Form\Extension\Core\Type\;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEmployee extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add("name")->add("details");
    }

    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array('data_class'=>'Aboutgcc\Test2Bundle\Entity\Employee'));
    }
    public function getName()
    {
        return "aboutgcc_test2_create_employee";
    }
}