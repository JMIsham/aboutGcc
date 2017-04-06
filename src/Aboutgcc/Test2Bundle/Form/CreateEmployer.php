<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 3/16/2017
 * Time: 3:46 PM
 */

namespace Aboutgcc\Test2Bundle\Form;
use Symfony\Component\Form\AbstractType;
use  Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CreateEmployer extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add("name")
            ->add("userId",NumberType::class)
            ->add("contactNum")
            ->add("RegNumber")
            ->add("doorAddress",TextType::class)
            ->add("country",NumberType::class)
            ->add("aboutUs",TextType::class)
        ;

    }
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>'Aboutgcc\Test2Bundle\Entity\Employer','csrf_protection' => false]);
    }
    public function getName()
    {
        return "aboutgcc_test2_create_employer";
    }

}