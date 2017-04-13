<?php
/**
 * Created by PhpStorm.
 * User: Isham
 * Date: 4/13/2017
 * Time: 8:23 AM
 */

namespace Aboutgcc\Test2Bundle\Form;



namespace Aboutgcc\Test2Bundle\Form;
use Symfony\Component\Form\AbstractType;
use  Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\OptionsResolver\OptionsResolver;

class CreatePost extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);
        $builder->add("subject")
            ->add("aboutJob",TextType::class)
            ->add("aboutSalary",TextType::class)
            ->add("aboutSkill",TextType::class)
        ;

    }
    public function setDefaultOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['data_class'=>'Aboutgcc\Test2Bundle\Entity\Employer','csrf_protection' => false]);
    }
    public function getName()
    {
        return "aboutgcc_test2_create_post";
    }

}